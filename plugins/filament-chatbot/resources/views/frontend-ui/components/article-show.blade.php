<div class="prose prose-sm py-5">
    <h1
        class="mt-0 text-2xl"
        x-html="showingArticle.title"
    ></h1>
    <div
        class="[&_:is(video,iframe)]:aspect-video [&_:is(video,iframe,img)]:h-auto [&_:is(video,iframe,img)]:rounded-md [&_:is(video,iframe,img)]:bg-foreground/5 [&_:is(video,iframe,img,audio)]:max-w-full [&_audio]:w-full"
        x-html="getFormattedString(showingArticle.content)"
    ></div>
</div>
