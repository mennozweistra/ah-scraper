// Based on https://www.freecodecamp.org/news/web-scraping-in-javascript-with-puppeteer/

import puppeteer from "puppeteer";

const getQuotes = async () => {
  // Start a Puppeteer session with:
  // - a visible browser (`headless: false` - easier to debug because you'll see the browser in action)
  // - no default viewport (`defaultViewport: null` - website page will in full width and height)
  const browser = await puppeteer.launch({
    headless: false,
    defaultViewport: null,
  });

  // Open a new page
  const page = await browser.newPage();

  // On this new page:
  // - open the "http://quotes.toscrape.com/" website
  // - wait until the dom content is loaded (HTML is ready)
  await page.goto("http://quotes.toscrape.com/", {
    waitUntil: "domcontentloaded",
  });

  const result = await page.evaluate(() => {
    const sections = document.querySelectorAll("div");
    return sections;
  });
  console.log("result: ");
  console.log(result);

  // // Convert the quoteList to an iterable array
  // // For each quote fetch the text and author
  // return Array.from(quoteList).map((quote) => {
  //   // Fetch the sub-elements from the previously fetched quote element
  //   // Get the displayed text and return it (`.innerText`)
  //   const text = quote.querySelector(".text").innerText;
  //   const author = quote.querySelector(".author").innerText;

  //   return { text, author };
  // });



  // Close the browser
  // await browser.close();
};

// Start the scraping
getQuotes();
