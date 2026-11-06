# Enjoy Naturally

WordPress theme for [Enjoy Naturally](https://enjoynaturally.com), a seasonal journal and a Nature's Turn publication. The brand on the site is Enjoy Naturally. Nature's Turn is named in the header note, the footer, and the about page, the same way the static preview did it.

This repository is the theme only. It does not include WordPress core.

## What you get

- Theme: `wp-content/themes/enjoy-naturally/`
- Layout 3 homepage: wordmark, November hero, kitchen / garden / home / kids bands, recipe of the week, newsletter panel
- Article, category, page, search, and 404 templates
- Fraunces and Source Sans 3, photographs, and the logo mark from the static preview
- Starter stories, pages, and categories (Appearance → Starter content, or WP-CLI)
- `scripts/package-theme.sh` and a GitHub Action that build `enjoy-naturally-theme.zip` for Bluehost

The homepage hero text and photo can be changed in **Appearance → Customize → Homepage hero**. The four story bands are the November layout in `inc/catalog.php`.

## Install on Bluehost

1. In Bluehost, create the WordPress site (one-click install) if it is not there yet. PHP 7.4 or newer. PHP 8.x is a good fit. You can use the temporary Bluehost address until the domain is pointed here.
2. Build or download the theme zip:
   - On your computer, from this repo: `./scripts/package-theme.sh`
   - The zip is written to `dist/enjoy-naturally-theme.zip`
   - Or, on GitHub, run the **Package theme** action and download the `enjoy-naturally-theme` artifact
3. In wp-admin go to **Appearance → Themes → Add New → Upload Theme**. Choose `enjoy-naturally-theme.zip`. Install, then **Activate**.
4. Go to **Appearance → Starter content** and click **Import starter content**.
   - Sets the site title to Enjoy Naturally
   - Sets permalinks to Post name (`/%postname%/`)
   - Discourages search engines until you turn that off
   - Creates categories, pages, four launch stories, menus, and the photographs
   - Moves the untouched “Hello world!” post and Sample Page to the trash
5. Open **Settings → Permalinks**, confirm **Post name**, and click Save once.
6. Leave **Settings → Reading** on **Your latest posts**. The designed homepage is `front-page.php`. It is not a page you assign in that screen.
7. In **Settings → General**, set the timezone you want for the newsroom (the launch notes use America/Los_Angeles) and confirm the admin email. Contact notes are mailed there.

### Git deploy instead of the zip

Clone this repo somewhere that is not the live WordPress directory. Copy only this folder into the existing site:

`wp-content/themes/enjoy-naturally`

Do not replace the Bluehost WordPress install with this repository. Core, `wp-config.php`, and uploads stay on the host.

Then activate the theme and run the starter import in wp-admin, or with WP-CLI from the WordPress directory:

```bash
wp theme activate enjoy-naturally
wp enjoy-naturally seed
```

`wp enjoy-naturally seed --replace` overwrites starter pages and posts that use the same slugs. `--keep-indexing` leaves search-engine visibility alone. `--skip-title` leaves the site title alone. `--skip-permalinks` leaves the permalink structure alone.

## Pages

The importer creates these pages:

| Page | Slug |
| --- | --- |
| A journal from Nature's Turn | `about` |
| The seasonal letter | `newsletter` |
| Contact the editors | `contact` |
| Privacy | `privacy` |
| Editorial disclosure | `editorial-disclosure` |
| Medical disclaimer | `medical-disclaimer` |

Privacy is also set as the WordPress privacy policy page. The header links Home, Kitchen, Garden, Home & Living, Family, Seasonal, and About. The footer lists the journal, every category, and the publication pages.

## Categories

| Category | Slug | Header label |
| --- | --- | --- |
| Food & Recipes | `food-recipes` | Kitchen |
| Garden | `garden` | Garden |
| Natural Home | `natural-home` | Home & Living |
| Family & Kids | `family-kids` | Family |
| Seasonal | `seasonal` | Seasonal |
| Herbs & Natural Wellness | `herbs` | footer only |
| Mindful Living | `mindful-living` | footer only |
| Outdoors | `outdoors` | footer only |
| Sustainable Living | `sustainable-living` | footer only |

Category URLs sit at the root, matching the preview: `/garden/`, `/food-recipes/`, and so on. `/category/garden/` redirects to `/garden/`.

## Launch stories

Published by the importer, dated November 5, 2026:

- What Is Freeze-Dried Fruit? How It Works and What Changes (`what-is-freeze-dried-fruit`)
- 10 Simple Ways to Make Everyday Snacking More Natural (`everyday-snacking-more-natural`)
- Healthy Snacks for Busy Families: A Practical Guide (`healthy-snacks-for-busy-families`)
- Creamy Wild Rice & Mushroom Soup (`creamy-wild-rice-mushroom-soup`)

These notes are still calendar previews, not posts. Publish a post with the same slug and it replaces the preview on the homepage and drops off the category calendar:

| Slug | Category |
| --- | --- |
| `apple-oat-crisp` | Food & Recipes |
| `garden-to-bed` | Garden |
| `plant-november` | Garden |
| `leaf-mulch` | Garden |
| `leaf-crown` | Natural Home |
| `scavenger-hunt` | Outdoors |
| `pinecone-feeders` | Family & Kids |
| `fall-decor` | Seasonal |

## Newsletter and contact

Both forms save a private **Reader notes** entry (editors and administrators). They also try to email the site admin address. A failed email does not drop the note. The journal does not send a confirmation to the reader, and it does not talk to Klaviyo yet.

The homepage copy says that plainly. When Klaviyo is connected, replace the form from a small plugin:

```php
add_filter('enjoy_naturally_newsletter_form_html', function () {
    return '<div class="klaviyo-form-XXXXXX"></div>';
});
```

`enjoy_naturally_contact_form_html` does the same for the contact form, if you later drop in WPForms or Fluent Forms. `enjoy_naturally_form_recipient` changes the address that receives mail.

Forms are built for a cached host: they do not print a nonce in the HTML. They use a honeypot, a same-site check, and a limit of 20 submissions an hour per address.

## Point enjoynaturally.com later

The domain is registered at Squarespace Domains and uses GoDaddy DNS (`domaincontrol.com`). When the Bluehost site looks right on its temporary URL:

1. In Bluehost, add `enjoynaturally.com` and `www.enjoynaturally.com` to the WordPress site and turn on the free SSL certificate.
2. In GoDaddy DNS, point the apex A record and `www` at the Bluehost IP (or the CNAME Bluehost gives you for `www`).
3. In WordPress, set **Settings → General** WordPress Address and Site Address to `https://enjoynaturally.com`.
4. Visit **Settings → Permalinks** and save again.
5. Uncheck **Discourage search engines** under **Settings → Reading** when you actually want to be indexed.

Until that switch, leave indexing off. The theme does not force a `noindex` tag of its own; it uses the WordPress privacy setting.

## Fonts and pictures

Fraunces and Source Sans 3 are SIL Open Font License. The license texts are in `assets/fonts/`. The photographs were made for Enjoy Naturally and ship with the theme so the starter import has pictures. Later stories can use the Media Library like any other WordPress site. The header is the “enjoy naturally” wordmark unless you set a custom logo under **Appearance → Customize → Site Identity**. The sun-and-leaf mark is the favicon until a Site Icon is uploaded.

## Theme files

`style.css`, `index.php`, `front-page.php`, `single.php`, `archive.php`, `page.php`, `header.php`, `footer.php`, `functions.php`, `404.php`, `search.php`, `screenshot.png`, and `assets/` from the static preview. Starter content lives in `demo/content.json` and is imported by `inc/starter.php`.
