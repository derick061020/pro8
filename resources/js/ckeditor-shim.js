/**
 * Shim que reemplaza @ckeditor/ckeditor5-build-classic usando los source packages
 * de ckeditor5 v44. Incluye todos los plugins del build clásico + SourceEditing.
 */
import {
    ClassicEditor as ClassicEditorBase,
    Autoformat,
    Bold, Italic, Underline, Strikethrough,
    BlockQuote,
    Essentials,
    Heading,
    Image, ImageCaption, ImageStyle, ImageToolbar, ImageUpload,
    Indent, IndentBlock,
    Link,
    List,
    MediaEmbed,
    Paragraph,
    PasteFromOffice,
    Table, TableToolbar,
    TextTransformation,
    Alignment,
    SourceEditing
} from 'ckeditor5';

class ClassicEditor extends ClassicEditorBase {
    static builtinPlugins = [
        Autoformat,
        Bold, Italic, Underline, Strikethrough,
        BlockQuote,
        Essentials,
        Heading,
        Image, ImageCaption, ImageStyle, ImageToolbar, ImageUpload,
        Indent, IndentBlock,
        Link,
        List,
        MediaEmbed,
        Paragraph,
        PasteFromOffice,
        Table, TableToolbar,
        TextTransformation,
        Alignment,
        SourceEditing
    ];

    static defaultConfig = {
        licenseKey: 'GPL',
        toolbar: {
            items: [
                'heading', '|',
                'bold', 'italic', 'link',
                'bulletedList', 'numberedList', '|',
                'outdent', 'indent', '|',
                'blockQuote', 'insertTable',
                'undo', 'redo'
            ]
        },
        image: {
            toolbar: ['imageStyle:inline', 'imageStyle:block', '|', 'imageTextAlternative']
        },
        table: {
            contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells']
        }
    };
}

export default ClassicEditor;
export { ClassicEditor };
