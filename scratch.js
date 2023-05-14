import fetch from 'node-fetch';

async function getBibleVerseContent(book, chapter, verse) {
    const bibleUrl = `https://my.bible.com/cs/bible/509/${book}.${chapter}.${verse}`;
    const response = await fetch(bibleUrl);
    const html = await response.text();
    const parser = new DOMParser();
    const doc = parser.parseFromString(html, 'text/html');
    const description = doc.querySelector('meta[name="description"]');
    return description.getAttribute('content');
}
  


console.log(getBibleVerseContent('JHN', 14, 6))