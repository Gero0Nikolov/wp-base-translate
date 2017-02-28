=== Plugin Name ===
Contributors: geronikolov
Donate link: http://geronikolov.com
Tags: translation, translating, translate, simple, modern
Requires at least: 3.1
Tested up to: 4.7
Stable tag: 1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

This plugin will allow you to create a multilingual website in just a few clicks.

== Description ==
Most of the translation plugins are built in very complicated and messy way...
That's why with WP_BASE_TRANSLATE you can <strong>create</strong>, <strong>edit</strong> or <strong>remove</strong> new languages in just a few clicks!

When the plugin is installed it performs the following things:
- Registers the <strong>Language Custom Post Type (CPT)</strong>
- Creates the <strong>_WP_PREFIX_page_language_relations</strong> table in your installation Database
- Adds page language metabox to Post / Page (it works with all CPTs as well)
- Adds avalilable languages metabox on which your page can be translated

<h1>How to add a new language?</h1>
The languages are registered in your database as Posts from <strong>Language CPT</strong>.
So to create a new language just go to <em><strong>Languages</strong> > <strong>Add new</strong> > <strong>Enter name (as title of the post)</strong> > <strong>Choose the icon of the language (optional as featured image)</strong></em>.

That's it!

<h1>How to translate?</h1>
As we mentioned above you can translate <strong>every</strong> post from a CPT in your WordPress installation.

To achieve that the plugin is registering a meta box called <em><strong>Available Languages</strong></em>, which represents the list of all available languages (as active buttons) on which you can translate the specific page.
Before you can start translating, you have to choose the current language of the page from the <em><strong>Page Language</strong></em> dropdown box. That will tell the plugin which is the parent language and connect the translated versions with it.
Once you do that, you just have to select the language on which you want to see your page and start translate the content in it.

That's all!

<h1>How it works on the front?</h1>
WP_BASE_TRANSLATE is meant to be easy, so if you currently have a language menu option connected on your website it'll work with it.
The only requirement for the plugin to work properly is to add <em><strong>lang</strong></em> parameter in your page URL.

<strong>Example:</strong> http://geronikolov.com/blog ---> http://geronikolov.com/blog?lang=bg (that will call the Blog page translated in <u>Bulgarian</u>)

<h2>What happens if I don't have that language implementation in my site?</h2>
WP_BASE_TRANSLATE don't come with built in language listing menu for the front-end, because of several reasons:
- Every website has it's unique design. That's why we can't build a universal language menu that will fit perfect for your idea.
- Everybodys taste is different. Some of us like the language menu to be at the top, others like it at the bottom. That's why we don't want to <em>"put you in a box"</em> with a hardcoded functionality.

<h2>How to build the Language listing menu?</h2>
Since the plugin doesn't provide a hardcoded menu, it provides a very flexible back-end method for implementing it with your website.
The core of the plugin gives you a function called <strong>get_registered_languages()</strong> which returns an <strong>Array</strong> of <strong>Objects</strong> which represent the registered <strong>Language objects</strong>.

Every language object has:
- Language ID: That's the ID of the Post in the database
- Language Name: That's the Title of the Post
- Language Code: That's the small version of your language title, for example if you have <strong>EN</strong> as title, the code will be <strong>en</strong>. In the navigation menu you should use it as value of the <strong>lang</strong> parameter.
- Language Slug: That's the post slug you've choosed from the WordPress Dashboard. By default it's smaller version of the title equal to the <strong>Language Code</strong>
- Language Link: That's the permalink of the <strong>Language Post Object</strong>.
- Language Author: That's the ID of the Administrator who created the language in your WordPress installation.
- Language Icon: That's the link to the language icon, which was selected from the WP Dashboard. Usually it's the flag of the <strong>Parent Country of the language</strong>.

The <strong>get_registered_languages()</strong> function can be found in the <strong>functions.php</strong> file positioned in the root folder of the plugin.

<h1>How to contribute?</h1>
You just have to clone the repository and build!

If you want to extend the <strong>functions.php</strong> file, make sure to wrap your <em><strong>new functions</strong></em> in the <strong>if ( !function_exists( "" ) ) {}</strong> statement.
If you want to extend the <strong>WP_BASE_TRANSLATE Core</strong> make sure to add small comment block above each of your functions.

<strong>The code block should look like:</strong>
<pre>
/*
*	Function name: example_function
*	Function arguments: $post_id [ INT ]
*	Function purpose: This function is just for an example.
*/
function example_function( $post_id ) { return "John Snow is alive!"; }
</pre>

<a href="https://github.com/Gero0Nikolov/wp-base-translate" target="_blank">Join the contributions!</a>

== Screenshots ==
1. Language registering
2. Page setup
3. Translated Page setup

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Use the Settings->Plugin Name screen to configure the plugin
1. (Make your instructions match the desired user flow for activating and installing your plugin. Include any steps that might be needed for explanatory purposes)
