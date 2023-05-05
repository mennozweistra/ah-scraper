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
  // - open the AH website
  // - wait until the dom content is loaded (HTML is ready)
  await page.goto("https://www.ah.nl/bonus", {
    waitUntil: "domcontentloaded",
  });

  // A popup with cookie preferences is shown
  // Click the button to decline cookies
  await page.waitForSelector("#decline-cookies");
  await page.click("#decline-cookies"); 

  // Wait until all <section>s are loaded
  await page.waitForSelector('#gall-card');

  // Find urls with pages that contain special offers
  const specialOfferPages = await page.evaluate(() => {
    // Fetch the first element with class "quote"
    // Get the displayed text and returns it
    const sections = document.querySelectorAll("section > div");

    // Convert the quoteList to an iterable array
    // For each quote fetch the text and author
    return Array.from(sections).map((section) => {
      try {
        const href = section.querySelector("a").getAttribute("href");
        if (href.startsWith("/bonus/groep")) {
          return "https://www.ah.nl" + href;
        } else throw "Unsupported url";
      }
      catch (e) {
        return false;
      }
    });
  });
  console.log(specialOfferPages);

    
  // const text = await page.evaluate(() => document.querySelector("section").textContent);
  // console.log(text);

  // const result = await page.evaluate(() => {
  //   const sections = document.querySelector("section");
  //   return sections;
  // });
  // console.log("result: ");
  // console.log(result);

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
  await browser.close();
};

// Start the scraping
getQuotes();
