<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Configuration;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class HelpController extends Controller
{
    /**
     * Devuelve el contenido de la ayuda contextual renderizado en HTML.
     *
     * @param string $topic Ruta relativa al archivo Markdown (ej. "documents/create")
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $topic)
    {
        $configuration = Configuration::first();

        if (!$configuration->enable_help_center) {
            return response()->json([
                'success' => false,
                'message' => 'El Centro de Ayuda está deshabilitado.'
            ], 403);
        }

        // Prevenir directory traversal
        $topic = str_replace('..', '', $topic);
        
        // Mapeos comunes (pueden expandirse)
        $mappings = [
            'persons/customers' => 'clientes',
            'persons/suppliers' => 'clientes', // O proveedores si existiera
            'person-types' => 'clientes/tipos-de-clientes', // Mapeo directo a manual
            'items' => 'items',
            
        // --- Mapeos agregados para Documentos y POS ---
            'pos/garage' => 'documents/pos',
            'pos' => 'documents/pos',
            'sale-notes/create' => 'documents/notas-de-venta',
            'sale-notes' => 'documents/notas-de-venta',
            'documents/create' => 'documents/create',
            'documents' => 'documents/list',
            
            // --- Mapeos agregados para Pre-Venta ---
            'sale-opportunities' => 'pre-venta/oportunidad-de-venta',
            'sale-opportunities/create' => 'pre-venta/oportunidad-de-venta',
            'quotations' => 'pre-venta/cotizaciones',
            'quotations/create' => 'pre-venta/cotizaciones',
            'contracts' => 'pre-venta/contratos',
            'contracts/create' => 'pre-venta/contratos',
            'order-notes' => 'pre-venta/pedidos',
            'order-notes/create' => 'pre-venta/pedidos',
            'technical-services' => 'pre-venta/servicio-tecnico',
            'technical-services/create' => 'pre-venta/servicio-tecnico',
        ];

        $mappedTopic = $mappings[$topic] ?? $topic;

        // Si es una petición de imagen (ej: clientes/img/foto.png)
        if (preg_match('/\.(png|jpg|jpeg|gif|svg)$/i', $mappedTopic)) {
            $imgPath = resource_path("docs/{$mappedTopic}");
            if (File::exists($imgPath)) {
                $mimeType = File::mimeType($imgPath);
                return response()->file($imgPath, ['Content-Type' => $mimeType]);
            }
            return response()->json(['success' => false, 'message' => 'Image not found'], 404);
        }

        $path = resource_path("docs/{$mappedTopic}.md");

        // Si el archivo exacto existe, lo devolvemos
        if (File::exists($path)) {
            if ($request->has('section')) {
                return $this->renderMarkdownSection($path, $mappedTopic, $request->input('section'));
            }
            return $this->renderMarkdown($path, $mappedTopic);
        }

        // Si no existe el archivo, buscamos si es un directorio
        $dirPath = resource_path("docs/{$mappedTopic}");

        if (File::isDirectory($dirPath)) {
            $files = File::files($dirPath);
            $manuals = [];

            foreach ($files as $file) {
                if ($file->getExtension() === 'md') {
                    $content = File::get($file->getRealPath());
                    $meta = $this->parseFrontMatter($content);
                    
                    $manuals[] = [
                        'title' => $meta['title'] ?? $file->getBasename('.md'),
                        'label' => $meta['sidebar_label'] ?? $meta['title'] ?? $file->getBasename('.md'),
                        'position' => (int) ($meta['sidebar_position'] ?? 99),
                        'topic' => "{$mappedTopic}/" . $file->getBasename('.md')
                    ];
                }
            }

            // Ordenar por posición
            usort($manuals, function($a, $b) {
                return $a['position'] <=> $b['position'];
            });

            return response()->json([
                'success' => true,
                'type' => 'directory',
                'manuals' => $manuals,
                'topic' => $topic
            ]);
        }

        // Documentación no encontrada, devolver mensaje por defecto
        $fallbackContent = "### ¡Hola! \n\nTodavía no hemos creado la guía para esta sección (`{$topic}`). \n\nSi necesitas asistencia urgente, por favor contacta con soporte.";
        
        return response()->json([
            'success' => true,
            'html' => (string) Str::markdown($fallbackContent),
            'topic' => $topic
        ]);
    }

    private function renderMarkdownSection($path, $topic, $sectionTitle)
    {
        $markdownContent = File::get($path);
        
        // Quitar FrontMatter
        $contentWithoutFrontMatter = preg_replace('/^---[\s]*[\r\n]+(.*?)[\r\n]+---[\s]*[\r\n]+/s', '', $markdownContent);
        
        // Extraer sección
        $sectionContent = $this->extractSection($contentWithoutFrontMatter, $sectionTitle);
        
        if (!$sectionContent) {
            return response()->json([
                'success' => true,
                'html' => "<p>Sección no encontrada: {$sectionTitle}</p>",
                'topic' => $topic
            ]);
        }

        $htmlContent = (string) Str::markdown($sectionContent);

        return response()->json([
            'success' => true,
            'type' => 'section',
            'html' => $htmlContent,
            'topic' => $topic,
            'section' => $sectionTitle
        ]);
    }

    private function extractSection($markdown, $sectionTitle)
    {
        $target = strtolower(trim($sectionTitle));
        $targetSlug = Str::slug($target);
        
        // Dividir por encabezados (##)
        $sections = preg_split('/^##\s+/m', $markdown);
        
        foreach ($sections as $s) {
            $lines = explode("\n", ltrim($s));
            $header = trim(array_shift($lines));
            $headerLower = strtolower($header);
            
            if ($headerLower === $target || Str::slug($headerLower) === $targetSlug) {
                // Devolver el contenido hasta el siguiente encabezado de nivel 2 o 1
                $content = implode("\n", $lines);
                // Si hay sub-encabezados (###), se incluyen. Pero si hay otro ##, se corta.
                // Sin embargo, preg_split ya hizo el corte por ^## 
                return trim($content);
            }
        }
        
        return null;
    }

    private function renderMarkdown($path, $topic)
    {
        $markdownContent = File::get($path);
        
        // Quitar FrontMatter antes de renderizar (tolerando espacios extra al final)
        $contentWithoutFrontMatter = preg_replace('/^---[\s]*[\r\n]+(.*?)[\r\n]+---[\s]*[\r\n]+/s', '', $markdownContent);
        
        // Soporte para Admonitions (bloques :::tipo) estilo Docusaurus
        $processedMarkdown = preg_replace_callback('/[\r\n]+:::(\w+)(.*?)[\r\n]+(.*?)\s*[\r\n]+:::[\s]*/s', function($matches) {
            $type = strtolower($matches[1]);
            $title = trim($matches[2]);
            $content = trim($matches[3]);
            
            // Si no hay título, usamos el nombre del tipo
            $displayTitle = $title ?: ucfirst($type);
            
            // Mapeo de iconos (FontAwesome)
            $icons = [
                'info' => 'fas fa-info-circle',
                'tip' => 'fas fa-lightbulb',
                'warning' => 'fas fa-exclamation-triangle',
                'danger' => 'fas fa-exclamation-circle',
                'note' => 'fas fa-sticky-note'
            ];
            $icon = $icons[$type] ?? 'fas fa-info-circle';

            return "\n\n<div class=\"admonition admonition-{$type}\">\n<p class=\"admonition-title\"><i class=\"{$icon} mr-1\"></i> {$displayTitle}</p>\n\n{$content}\n\n</div>\n\n";
        }, $contentWithoutFrontMatter);

        $htmlContent = (string) Str::markdown($processedMarkdown);

        // Corregir rutas relativas de imágenes a absolutas del endpoint
        $topicDir = dirname($topic);
        if ($topicDir === '.') {
            $topicDir = '';
        } else {
            $topicDir .= '/';
        }
        $htmlContent = str_replace('src="img/', 'src="/api/help-center/' . $topicDir . 'img/', $htmlContent);

        return response()->json([
            'success' => true,
            'type' => 'file',
            'html' => $htmlContent,
            'topic' => $topic
        ]);
    }

    private function parseFrontMatter($content)
    {
        // Se corrigió para permitir espacios después de los guiones `--- `
        $pattern = '/^---[\s]*[\r\n]+(.*?)[\r\n]+---[\s]*[\r\n]+/s';
        if (preg_match($pattern, $content, $matches)) {
            $frontMatter = $matches[1];
            $data = [];
            foreach (explode("\n", $frontMatter) as $line) {
                if (strpos($line, ':') !== false) {
                    list($key, $value) = explode(':', $line, 2);
                    $data[trim($key)] = trim(trim($value), '"\' ');
                }
            }
            return $data;
        }
        return [];
    }
}
