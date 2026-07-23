<template>
    <span v-if="topic" class="context-help-wrap">
        <el-popover placement="bottom-end" trigger="click" :width="popoverWidth" popper-class="context-help-popover-wrap">
            <div class="context-help-popover">
                <div class="context-help-popover__title">{{ topic.title }}</div>
                <ul class="context-help-popover__list">
                    <li v-for="(line, idx) in topic.summary_lines" :key="idx">{{ line }}</li>
                </ul>
                <p v-if="topic.source_doc_repo_relative && topic.source_doc_repo_relative.indexOf('(') !== 0" class="context-help-popover__source small text-muted mb-2">
                    Fuente en repo de docs: {{ topic.source_doc_repo_relative }}
                </p>
                <a
                    v-if="topic.manual_full_url"
                    class="context-help-popover__link small"
                    :href="topic.manual_full_url"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    Ver documentación completa
                </a>
            </div>
            <button slot="reference" type="button" class="btn btn-link btn-sm context-help-trigger p-0 align-baseline">
                ¿Ayuda?
            </button>
        </el-popover>
    </span>
</template>

<script>
import manifest from '../../help/es/context-help.json';

export default {
    name: 'ContextHelp',
    props: {
        configuration: {
            type: Object,
            required: false,
            default: () => ({})
        }
    },
    data() {
        return {
            manifest,
            popoverWidth: 340,
        };
    },
    computed: {
        topic() {
            if (this.configuration && !this.configuration.enable_interactive_tours) {
                return null;
            }
            return this.resolveTopic(typeof window !== 'undefined' ? window.location.pathname : '');
        },
    },
    methods: {
        normalizePath(pathname) {
            if (!pathname) {
                return '/';
            }
            const trimmed = pathname.replace(/\/+$/, '');
            return trimmed || '/';
        },
        resolveTopic(pathname) {
            const path = this.normalizePath(pathname);
            const topics = this.manifest.topics || [];
            let best = null;
            let bestLen = -1;

            topics.forEach((topic) => {
                const patterns = topic.path_patterns || [];
                patterns.forEach((pattern) => {
                    const pat = this.normalizePath(pattern);
                    if (path === pat || path.startsWith(`${pat}/`)) {
                        if (pat.length > bestLen) {
                            best = topic;
                            bestLen = pat.length;
                        }
                    }
                });
            });

            return best;
        },
    },
};
</script>

<style>
.context-help-trigger {
    font-size: 13px;
    font-weight: 500;
    text-decoration: none !important;
    white-space: nowrap;
}

.context-help-trigger:hover,
.context-help-trigger:focus {
    text-decoration: underline !important;
}

.context-help-popover-wrap .context-help-popover__title {
    font-weight: 600;
    font-size: 14px;
    margin-bottom: 8px;
    color: #1f2937;
}

.context-help-popover-wrap .context-help-popover__list {
    margin: 0 0 8px;
    padding-left: 1.15rem;
    font-size: 13px;
    line-height: 1.45;
    color: #374151;
}

.context-help-popover-wrap .context-help-popover__link {
    font-weight: 500;
}

html.dark .context-help-popover-wrap .context-help-popover__title {
    color: #f3f4f6;
}

html.dark .context-help-popover-wrap .context-help-popover__list {
    color: #e5e7eb;
}
</style>
