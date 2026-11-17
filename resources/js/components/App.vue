<template>
    <div v-if="article" class="fixed right-4 bottom-4 z-50 w-[calc(100vw-2rem)] max-w-md rounded-lg border border-slate-700 bg-slate-900 px-6 py-4 text-slate-200 shadow-lg shadow-black/40" role="status" aria-live="polite">
        Добавлена новая статья:
        <a class="font-semibold text-white underline underline-offset-4" :href="`/articles/${article.id}`">{{ article.title }}</a>
    </div>
</template>
<script>

export default {
    data() {
        return { article: null, dismissTimer: null };
    },
    created() {
        window.Echo.private('articles').listen('NewArticleEvent', (article) => {
            window.clearTimeout(this.dismissTimer);
            this.article = article;
            this.dismissTimer = window.setTimeout(() => {
                this.article = null;
                this.dismissTimer = null;
            }, 10000);
        });
    },
    beforeUnmount() {
        window.clearTimeout(this.dismissTimer);
        window.Echo.leave('articles');
    },
};

</script>
