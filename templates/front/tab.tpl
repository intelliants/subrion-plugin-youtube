{if !empty($item.youtube_video) && isset($youtube_video)}
    {capture append='tabs_content' name='youtube_video'}
        <div id="youtube_video" class="ia-wrap text-center">
            <iframe src="https://www.youtube.com/embed/{$youtube_video}" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
        </div>
    {/capture}
{/if}

{ia_print_css files='_IA_URL_modules/youtube/templates/front/css/style'}