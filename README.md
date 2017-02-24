#WP_BASE_TRANSLATE
The Simplest, Freest &amp; OpenSourcest <strong>WordPress plugin</strong> out there!

##How it works?
Most of the translation plugins are built in very complicated and messy way...
That's why with WP_BASE_TRANSLATE you can <strong>create</strong>, <strong>edit</strong> or <strong>remove</strong> new languages in just a few clicks!

When the plugin is installed it performs the following things:
- Registers the <strong>Languages Custom Post Type (CPT)</strong>
- Creates the <strong>_WP_PREFIX_page_language_relations</strong> table in your installation Database
- Adds default language metabox to Post / Page (it works with all CPTs as well)
- Adds avalilable languages metabox on which your page can be translated

##How to add a new language?
The languages are registered in your database as Posts from <strong>Language CPT</strong>.
So to create a new language just go to <em><strong>Languages</strong> > <strong>Add new</strong> > <strong>Enter name (as title of the post)</strong> > <strong>CHoose the icon of the language (optional as featured image)</strong></em>.
That's it!

##How to translate?
As we mentioned above you can translate <strong>every</strong> post from a CPT in your WordPress installation.
To achieve that the plugin is registering a meta box called <em><strong>Available Languages</strong></em>, which represents the list of all available languages (as active buttons) on which you can translate the specific page.
Before you can start translating, you have to choose the current language of the page from the <em><strong>Page Language</strong></em> dropdown box. That will tell the plugin which is the parent language and connect the translated versions with it.
Once you do that, you just have to select the language on which you want to see your page and start translate the content in it.
That's all!

##How it works on the front?
WP_BASE_TRANSLATE is meant to be easy, so if you currently have a language menu option connected on your website it'll work with it.
The only requirement for the plugin to work properly is to add <em><strong>lang</strong></em> parameter in your page URL.

<strong>Example:</strong>http://geronikolov.com/blog ---> http://geronikolov.com/blog?lang=bg (that will call the Stories page translated in <u>Bulgarian</u>)

###What happens if I don't have that language implementation in my site?
WP_BASE_TRANSLATE don't come with built in language listing menu for the front-end, becuase of several reasons:
- Every website has it's unique design. That's why we can't build a universal language manu that will fit perfect for your idea.
- Everybody taste is different. Some of us like the language manu to be at the top, others like it at the bottom. That's why we don't want to <em>"put you in a box"</em> with a hardcoded functionality.

That is the only thing, you'll have to do on your own, if you don't use different sub-domains for your different languages.

###How to build the Language listing menu?
Since the plugin doesn't provide a hardcoded menu, it provides a very flexible back-end method for implementing it with your website.
The core of the plugin gives you a function called <strong>get_registered_languages();</strong> which returns an <strong>Array</strong> of <strong>Objects</strong> which represent the registered <strong>Language objects</strong>.

Every language object has:
- Language ID: That's the ID of the Post in the database
- Language Name: That's the Title of the Post
- Language Code: That's the small version of your language title, for example if you have <strong>EN</strong> as title, the code will be <strong>en</strong>. In the navigation menu you should use it as value of the <strong>lang</strong> parameter.
- Language Slug: That's the post slug you've choosed from the WordPress Dashboard. By default it's smaller version of the title equal to the <strong>Language Code</strong>
- Language Link: That's the permalink of the <strong>Language Post Object</strong>.
- Language Author: That's the ID of the Administrator who created the language in your WordPress installation.
- Language Icon: That's the link to the language icon, which was selected from the WP Dashboard. Usually it's the flag of the <strong>Parent Country of the language</strong>.

The <strong>get_registered_languages()</strong> function can be found in the <strong>functions.php</strong> file positioned in the root folder of the plugin.

##How to contribute?
You just have to clone the repository and build!
If you want to extend the <strong>functions.php</strong> file, make sure to wrap your <em><strong>new functions</strong></em> in the <strong>if ( !function_exists( "" ) ) {}</strong> statement.
If you want to extend the <strong>WP_BASE_TRANSLATE Core</strong> make sure to add small comment block above each of your functions.

<strong>The code block should look like:</strong>
/*
*	Function name: example_function
*	Function arguments: $post_id [ INT ]
*	Function purpose: This function is just for an example.
*/
function example_function( $post_id ) { return "John Snow is alive!"; }

#That's it!
