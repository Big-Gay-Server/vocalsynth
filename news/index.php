<h1> Updates </h1>

<div id="rss-feed-container">
    <p>Loading latest news...</p>
</div>


<script>
    async function loadRSS() {
    const FEED_URL = 'https://vocalsynth.lunarconstruct.net/news/feed.php';
    const container = document.getElementById('rss-feed-container');

    try {
        const response = await fetch(FEED_URL);
        const text = await response.text();
        const xml = new window.DOMParser().parseFromString(text, "text/xml");
        const items = xml.querySelectorAll("item");

        let html = '<ul class="rss-list">';
        items.forEach(el => {
            const fullTitle = el.querySelector("title").textContent;
            const link = el.querySelector("link").textContent;
            const date = new Date(el.querySelector("pubDate").textContent).toLocaleDateString();
            const description = el.querySelector("description") ? el.querySelector("description").textContent : "";

            // This regex looks for [TEXT] and separates it from the rest
            // match[1] = the tag (COVER/NEWS), match[2] = the actual title
            const tagMatch = fullTitle.match(/^.*\[(.*?)\]\s*(.*)$/);
            
            let displayTitle = fullTitle;
            let tagHtml = '';

            if (tagMatch) {
                const tagType = tagMatch[1].toLowerCase(); // "cover" or "news"
                tagHtml = `<span class="rss-tag tag-${tagType}">${tagMatch[1]}</span>`;
                displayTitle = tagMatch[2];
            }

            html += `
                <li class="rss-item">
                    <div class="rss-header">
                        <a href="${link}" class="rss-title"><strong>${displayTitle}</strong></a>
                        ${tagHtml} 
                    </div>
                    <div class="rss-date">${date}</div>
                    <div class="rss-preview">${description}</div>
                    <a href="${link}" class="rss-read-more">Read Full Post →</a>
                </li>`;
        });
        html += '</ul>';
        container.innerHTML = html;
    } catch (err) {
        container.innerHTML = '<p>Failed to load news feed.</p>';
        console.error(err);
    }
}

loadRSS();
</script>
