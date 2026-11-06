<?php
/**
 * November layout: story bands, calendar notes, and launch articles.
 *
 * A published post with the same slug replaces a calendar preview on the
 * homepage and on the category archive.
 *
 * @package Enjoy_Naturally
 */

defined('ABSPATH') || exit;

/**
 * Stories and calendar notes keyed by slug.
 *
 * @return array<string, array<string, mixed>>
 */
function en_catalog() {
	static $items = null;
	if (null !== $items) {
		return $items;
	}

	$items = array(
		'what-is-freeze-dried-fruit'       => array(
			'title'    => 'What Is Freeze-Dried Fruit? How It Works and What Changes',
			'kicker'   => 'Explainer',
			'excerpt'  => 'Freeze-dried fruit is real fruit with most of the water lifted out by freezing and a vacuum. The pieces stay light and crisp. Here is what changes, and what does not.',
			'image'    => 'freeze-dried.jpg',
			'alt'      => 'A ceramic bowl of crisp freeze-dried strawberry and apple pieces on linen, with two fresh strawberries beside it.',
			'width'    => 1600,
			'height'   => 900,
			'category' => 'food-recipes',
			'kind'     => 'article',
		),
		'everyday-snacking-more-natural'   => array(
			'title'    => '10 Simple Ways to Make Everyday Snacking More Natural',
			'kicker'   => 'Everyday',
			'excerpt'  => 'More natural, at home, means snacks you recognize and portions that fit the day. Ten habits, including what the word natural does not mean on a label.',
			'image'    => 'snacks-board.jpg',
			'alt'      => 'A wooden board with apple slices, almonds, yogurt, cucumber sticks, and freeze-dried berries on cream linen.',
			'width'    => 1600,
			'height'   => 900,
			'category' => 'food-recipes',
			'kind'     => 'article',
		),
		'apple-oat-crisp'                  => array(
			'title'    => 'Apple Oat Crisp with Maple',
			'kicker'   => 'Baking',
			'excerpt'  => 'Sliced apples, oats, a spoon of maple, and a warm oven. The full recipe is still in testing so the times and pan size are honest before we publish them.',
			'image'    => 'apple-crisp.jpg',
			'alt'      => 'A rustic apple oat crisp in a ceramic baking dish, golden on top, with a wooden spoon beside it.',
			'width'    => 1000,
			'height'   => 1000,
			'category' => 'food-recipes',
			'kind'     => 'preview',
		),
		'creamy-wild-rice-mushroom-soup'   => array(
			'title'    => 'Creamy Wild Rice & Mushroom Soup',
			'kicker'   => 'Recipe of the week',
			'excerpt'  => 'Hearty, comforting, and perfect for chilly November nights.',
			'image'    => 'soup-week.jpg',
			'alt'      => 'Creamy wild rice and mushroom soup in a white bowl beside a torn piece of bread.',
			'width'    => 1600,
			'height'   => 1200,
			'category' => 'food-recipes',
			'kind'     => 'recipe',
		),
		'garden-to-bed'                    => array(
			'title'    => 'Putting the Garden to Bed',
			'kicker'   => 'Tips',
			'excerpt'  => 'Clear spent vines, leave a few seed heads for birds, and cover bare soil before the ground locks up. A full checklist is in the works for this month.',
			'image'    => 'garden-autumn.jpg',
			'alt'      => 'A gardener in a wool hat and green coat kneeling beside wooden raised beds and a crate of kale in late autumn.',
			'width'    => 1600,
			'height'   => 1200,
			'category' => 'garden',
			'kind'     => 'preview',
		),
		'plant-november'                   => array(
			'title'    => 'What to Plant in November',
			'kicker'   => 'Planning',
			'excerpt'  => 'Garlic where winters allow it, a pot of hardy greens, and a paper plan for spring so January is not a blank page. Planting notes are on the calendar, matched to climate instead of a single national date.',
			'image'    => 'leaf-mulch.jpg',
			'alt'      => 'Dark garden soil under a layer of autumn leaves, with a kale stem still growing through the mulch.',
			'width'    => 1000,
			'height'   => 1000,
			'category' => 'garden',
			'kind'     => 'preview',
		),
		'leaf-mulch'                       => array(
			'title'    => 'Leaf Mulch Magic',
			'kicker'   => 'Soil health',
			'excerpt'  => 'Fallen leaves are a blanket, not a mess. Shred them if you can, lay them on bare soil, and let winter do the slow work. The how-to will cover depth, matted leaves, and what not to pile against a trunk.',
			'image'    => 'leaf-mulch.jpg',
			'alt'      => 'Close view of fallen leaves spread as mulch around a leafy green in a raised bed.',
			'width'    => 1000,
			'height'   => 1000,
			'category' => 'garden',
			'kind'     => 'preview',
		),
		'leaf-crown'                       => array(
			'title'    => 'Make a Leaf Crown',
			'kicker'   => 'Craft',
			'excerpt'  => 'A walk, a handful of flexible stems or a paper band, and leaves tucked in until it looks like a crown. A full how-to is coming for weekend afternoons, with a note on leaves that stain fingers.',
			'image'    => 'kids-crafts.jpg',
			'alt'      => 'Two children at a wooden table tying twine around pinecones, with autumn leaves scattered between them.',
			'width'    => 1600,
			'height'   => 1200,
			'category' => 'natural-home',
			'kind'     => 'preview',
		),
		'scavenger-hunt'                   => array(
			'title'    => 'Nature Scavenger Hunt',
			'kicker'   => 'Outdoor fun',
			'excerpt'  => 'A short list of things to find on a block or a trail: a color, a sound, and something soft. The printable version is still being drawn so the type is large enough for small hands.',
			'image'    => 'garden-autumn.jpg',
			'alt'      => 'An autumn garden with wooden beds, fallen leaves, and a person harvesting kale into a crate.',
			'width'    => 1600,
			'height'   => 1200,
			'category' => 'outdoors',
			'kind'     => 'preview',
		),
		'pinecone-feeders'                 => array(
			'title'    => 'Pinecone Bird Feeders',
			'kicker'   => 'Activity',
			'excerpt'  => 'A pinecone, a thin coat of sunflower seed butter if nuts are not right for your house, and birdseed. The careful version will cover allergies, hanging height, and what to skip if seed attracts more than birds.',
			'image'    => 'pinecone-feeder.jpg',
			'alt'      => 'A pinecone coated in seeds and tied with twine, resting on a wooden table with a few loose seeds and a leaf.',
			'width'    => 1000,
			'height'   => 1000,
			'category' => 'family-kids',
			'kind'     => 'preview',
		),
		'healthy-snacks-for-busy-families' => array(
			'title'    => 'Healthy Snacks for Busy Families: A Practical Guide',
			'kicker'   => 'Practical guide',
			'excerpt'  => 'Healthy, here, means everyday foods in amounts that fit the people eating them. A simple pattern, five fast plates, and the safety notes that matter.',
			'image'    => 'family-snacks.jpg',
			'alt'      => 'A parent and a school-age child packing fruit, cheese, and cut vegetables into a lunchbox on a kitchen counter.',
			'width'    => 1600,
			'height'   => 900,
			'category' => 'family-kids',
			'kind'     => 'article',
		),
		'fall-decor'                       => array(
			'title'    => 'Natural Fall Decor Ideas',
			'kicker'   => 'Inspiration',
			'excerpt'  => 'Branches in a jar, a bowl of gourds, and dried orange slices if you want the room to smell like the oven. No plastic required. The story is queued behind the November kitchen pieces.',
			'image'    => 'home-cozy.jpg',
			'alt'      => 'A cream linen sofa with a knit throw, a wood side table, a lit candle, and a vase of olive branches.',
			'width'    => 1600,
			'height'   => 1200,
			'category' => 'seasonal',
			'kind'     => 'preview',
		),
	);

	return $items;
}

/**
 * One catalog record, or null.
 *
 * @param string $slug Post or preview slug.
 * @return array<string, mixed>|null
 */
function en_catalog_item($slug) {
	$all = en_catalog();
	return isset($all[ $slug ]) ? $all[ $slug ] : null;
}

/**
 * Homepage bands in Layout 3 order.
 *
 * @return array<int, array<string, mixed>>
 */
function en_home_bands() {
	return array(
		array(
			'id'          => 'kitchen',
			'class'       => 'band band-kitchen',
			'eyebrow'     => __('In the kitchen', 'enjoy-naturally'),
			'title'       => __('Warm meals, made with the season', 'enjoy-naturally'),
			'dek'         => __('Nourishing recipes with squash, apples, greens, and cozy flavors.', 'enjoy-naturally'),
			'photo_first' => true,
			'cards_class' => 'band-cards cards',
			'cards'       => array( 'what-is-freeze-dried-fruit', 'everyday-snacking-more-natural', 'apple-oat-crisp' ),
			'photo'       => array(
				'file'   => 'kitchen-bowl.jpg',
				'alt'    => __('A ceramic bowl of farro with roasted squash, zucchini, chickpeas, and herbs.', 'enjoy-naturally'),
				'width'  => 1000,
				'height' => 1000,
			),
		),
		array(
			'id'          => 'garden',
			'class'       => 'band band-garden',
			'eyebrow'     => __('In the garden', 'enjoy-naturally'),
			'title'       => __('Late-season care, early-spring plans', 'enjoy-naturally'),
			'dek'         => __('Tidy, tend, and plant now for a thriving garden when the days grow longer.', 'enjoy-naturally'),
			'photo_first' => false,
			'cards_class' => 'band-cards cards',
			'cards'       => array( 'garden-to-bed', 'plant-november', 'leaf-mulch' ),
			'photo'       => array(
				'file'   => 'garden-autumn.jpg',
				'alt'    => __('A gardener in a wool hat kneeling by raised beds and a wooden crate of kale on a leaf-covered path.', 'enjoy-naturally'),
				'width'  => 1600,
				'height' => 1200,
			),
		),
		array(
			'id'          => 'home',
			'class'       => 'band band-home',
			'eyebrow'     => __('Around the home', 'enjoy-naturally'),
			'title'       => __('Cozy, calm, and naturally made', 'enjoy-naturally'),
			'dek'         => __('Simple ways to create a warm, earthy home this season.', 'enjoy-naturally'),
			'photo_first' => true,
			'cards_class' => 'band-cards cards',
			'cards'       => array( 'leaf-crown', 'scavenger-hunt', 'pinecone-feeders' ),
			'photo'       => array(
				'file'   => 'home-cozy.jpg',
				'alt'    => __('A cream linen sofa with a chunky knit throw, a round wood table, a candle, and olive branches.', 'enjoy-naturally'),
				'width'  => 1600,
				'height' => 1200,
			),
		),
		array(
			'id'          => 'kids',
			'class'       => 'band band-kids',
			'eyebrow'     => __('With the kids', 'enjoy-naturally'),
			'title'       => __('Nature play and seasonal crafts', 'enjoy-naturally'),
			'dek'         => __('Fun, hands-on ideas to get little ones outdoors and creative.', 'enjoy-naturally'),
			'photo_first' => false,
			'cards_class' => 'band-cards cards cards-two',
			'cards'       => array( 'healthy-snacks-for-busy-families', 'fall-decor' ),
			'photo'       => array(
				'file'   => 'kids-crafts.jpg',
				'alt'    => __('Two children smiling as they tie twine around pinecones at a wooden table covered with autumn leaves.', 'enjoy-naturally'),
				'width'  => 1600,
				'height' => 1200,
			),
		),
	);
}

/**
 * Primary navigation, matching the static header.
 *
 * @return array<int, array<string, string>>
 */
function en_primary_links() {
	return array(
		array(
			'label' => __('Home', 'enjoy-naturally'),
			'kind'  => 'home',
			'slug'  => '',
		),
		array(
			'label' => __('Kitchen', 'enjoy-naturally'),
			'kind'  => 'category',
			'slug'  => 'food-recipes',
		),
		array(
			'label' => __('Garden', 'enjoy-naturally'),
			'kind'  => 'category',
			'slug'  => 'garden',
		),
		array(
			'label' => __('Home & Living', 'enjoy-naturally'),
			'kind'  => 'category',
			'slug'  => 'natural-home',
		),
		array(
			'label' => __('Family', 'enjoy-naturally'),
			'kind'  => 'category',
			'slug'  => 'family-kids',
		),
		array(
			'label' => __('Seasonal', 'enjoy-naturally'),
			'kind'  => 'category',
			'slug'  => 'seasonal',
		),
		array(
			'label' => __('About', 'enjoy-naturally'),
			'kind'  => 'page',
			'slug'  => 'about',
		),
	);
}

/**
 * Footer category list in the static site's order.
 *
 * @return array<string, string> slug => label
 */
function en_explore_links() {
	return array(
		'food-recipes'       => __('Food & Recipes', 'enjoy-naturally'),
		'garden'             => __('Garden', 'enjoy-naturally'),
		'herbs'              => __('Herbs & Natural Wellness', 'enjoy-naturally'),
		'natural-home'       => __('Natural Home', 'enjoy-naturally'),
		'family-kids'        => __('Family & Kids', 'enjoy-naturally'),
		'mindful-living'     => __('Mindful Living', 'enjoy-naturally'),
		'seasonal'           => __('Seasonal', 'enjoy-naturally'),
		'outdoors'           => __('Outdoors', 'enjoy-naturally'),
		'sustainable-living' => __('Sustainable Living', 'enjoy-naturally'),
	);
}

/**
 * Footer publication links.
 *
 * @return array<string, string> slug => label
 */
function en_publication_links() {
	return array(
		'about'                 => __('About', 'enjoy-naturally'),
		'newsletter'            => __('Newsletter', 'enjoy-naturally'),
		'contact'               => __('Contact', 'enjoy-naturally'),
		'privacy'               => __('Privacy', 'enjoy-naturally'),
		'editorial-disclosure'  => __('Editorial disclosure', 'enjoy-naturally'),
		'medical-disclaimer'    => __('Medical disclaimer', 'enjoy-naturally'),
	);
}

/**
 * Launch stories listed in the footer, in reading order.
 *
 * @return string[]
 */
function en_journal_slugs() {
	return array(
		'what-is-freeze-dried-fruit',
		'everyday-snacking-more-natural',
		'healthy-snacks-for-busy-families',
		'creamy-wild-rice-mushroom-soup',
	);
}
