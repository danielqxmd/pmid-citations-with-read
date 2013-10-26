=== PMID Citation with Read ===
Contributors: QxMD
Tags: pmid, PubMed, read, qxmd, cite, citation, science, academia, endnotes, footnotes, mendeley, research, bibliography, references
Stable tag: trunk
Requires at least: 3.1.2
Tested up to: 3.6.1

This plugin allows you to add PubMed citations to wordpress sites with deep integration to the Read by QxMD service.

== Description ==

This plugin allows you to add PubMed citations to the body of any page/post as well as create a list of references at the bottom of any page/post.  Links are integrated with the Read by QxMD service to allow rapid access to full text articles through institutional subscriptions on both mobile and web. 

= Features =

* Creates an input box on your post composition page where you can input PubMed IDs.
* PubMed IDs are then stored in the database along with your post, and this data is used to create a references block at the bottom of your post.

== Screenshots ==

1. The references listed at the bottom of a blog post.
2. PMID Citations with Read entry field above the update button.
3. Finding a PMID on PubMed.com
4. Finding a PMID on ReadbyQxMD.com

== Upgrade Notice ==

= 1.0.0 =
Release

== Changelog ==

= 1.0.0 =
* Release

== Installation ==

1. Download the plugin

2. Extract the contents of pmid-citations-with-read.zip to wp-content/plugins/ folder. You should get a folder called pmid-citations-with-read.

3. Activate the Plugin in WP-Admin.

4. Go to your composition page, and enter in your comma separated list of PubMed IDs in the field at the top right hand of the page.

5. Or, add a citation anywhere on the page/post using shortcode [PMID]insert-PMID-here[/PMID]

== Frequently Asked Questions ==

= How does the interface with Read by QxMD provide full text access? =

When a reference is clicked or tapped, the Read by QxMD service recognizes the users device.  iOS devices get deep linked into the Read by QxMD app which tries to find full text access to the reference.  Other web enabled devices or desktop/laptop computers are provide a web interface that attempts to locate full text access.

= Can I customize what is displayed? =

The plugin uses the css class 'pmidcitationplus' in the div that surrounds the references block. If you would like, you may use this class to then style it by adding code to your *style.css* file of your theme folder.

For more information, please visit http://www.qxmd.com/pmid-citations-with-read

= Who influenced the development of this plugin? =

PMID Citations with Read is adapted from PMID Citations Plus by Dan Patrick
http://mdpatrick.com/

= Support =

Email us at contact@qxmd.com
