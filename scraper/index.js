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

  let counter = 0;
  let blocked = false;
  specialOfferPages.forEach(async specialOfferPage => {
    if (!specialOfferPage) return;
    if (specialOfferPage.includes('/FREE')) return;
    while (blocked) {
      // Wait 1 second
      await new Promise(resolve => setTimeout(resolve, 1000));
    }
    counter++;
    console.log(counter + ". " + specialOfferPage);
    blocked = true;
    // if (counter > 1) {
    //   blocked = false;
    //   return;
    // }

    const sop = await browser.newPage();
    await sop.goto(specialOfferPage, {
      waitUntil: 'networkidle0',
    });

    // Force all articles to have data instead of placeholders
    // First scroll all the way down the page
    // Then scroll up in small steps to the top
    await sop.evaluate(() => document.querySelector('#footer').scrollIntoView());
    await sop.evaluate(async () => { 
      let done = false;
      while (!done) {
        window.scrollBy(0, -100, "smooth");
        await new Promise(function(resolve) { 
          setTimeout(resolve, 1)
        });
        if (window.scrollY == 0) done = true;
      }
    });
    console.log("Number of articles: " + (await sop.$$('article')).length);

    // Find urls with pages that contain special offers
    const specialOffers = await sop.evaluate(() => {
      let result = [];
      const articles = document.querySelectorAll("article > div > a");
      return Array.from(articles).map((article) => {
        try {
          const title = article.getAttribute("title");
          const href = article.getAttribute("href");
          return [title, href];
        }
        catch (e) {
          return false;
        }
      });

      return result;
    });
    console.log(specialOffers);

    blocked = false;
  });

  // Close the browser
  // await browser.close();
};

// Start the scraping
getQuotes();


