## $5 Tech Unlocked 2021!
[Buy and download this Book for only $5 on PacktPub.com](https://www.packtpub.com/product/wordpress-web-application-development-second-edition/9781782174394)
-----
*If you have read this book, please leave a review on [Amazon.com](https://www.amazon.com/gp/product/1782174397).     Potential readers can then use your unbiased opinion to help them make purchase decisions. Thank you. The $5 campaign         runs from __December 15th 2020__ to __January 13th 2021.__*

# Wordpress Web Application Development

<a href="https://www.packtpub.com/web-development/wordpress-web-application-development?utm_source=github&utm_medium=repository&utm_campaign="><img src="https://prod.packtpub.com/media/catalog/product/cache/ecd051e9670bd57df35c8f0b122d8aea/9/7/9781787126800.png" alt="" height="256px" align="right"></a>

This is the code repository for [Wordpress Web Application Development](https://www.packtpub.com/web-development/wordpress-web-application-development?utm_source=github&utm_medium=repository&utm_campaign=), published by Packt.

**Building robust web apps easily and efficiently**

## What is this book about?
* Develop extendable plugins with the use of WordPress features in core modules
* Develop pluggable modules to extend the core features of WordPress as independent modules
* Manage permissions for a wide range of content types in web applications based on different user types 
* Follow WordPress coding standards to develop reusable and maintainable code 
* Build and customize themes beyond conventional web layouts
* Explore the power of core database tables and understand the limitations when designing database tables for large applications

This book covers the following exciting features:
WordPress is one of the most rapidly expanding markets on the Web. Learning how to build complex and scalable web applications will give you the ability and knowledge to step into the future of WordPress. WordPress 4.7 introduces some exciting new improvements and several bug fixes, which further improve the entire development process.This book is a practical, scenario-based guide to expanding the power of the WordPress core modules to develop modular and maintainable real-world applications from scratch. This book consistently emphasizes adapting WordPress features into web applications. It will walk you through the advanced usages of existing features such as access controlling; database handling; custom post types; pluggable plugins; content restrictions; routing; translation; caching; and many more, while you build the backend of a forum management application.


If you feel this book is for you, get your [copy](https://www.amazon.com/dp/1787126803) today!

<a href="https://www.packtpub.com/?utm_source=github&utm_medium=banner&utm_campaign=GitHubBanner"><img src="https://raw.githubusercontent.com/PacktPublishing/GitHub/master/GitHub.png" 
alt="https://www.packtpub.com/" border="5" /></a>

## Instructions and Navigations
All of the code is organized into folders. For example, Chapter02.

The code will look like the following:
```
add_action('pre_get_posts', 'search_restrictions');
function search_restrictions($query) {
if($query->is_search && $query->is_main_query() && !is_admin()){
$search_blocked_ids = array('24','100');
$search_allowed_types = array('wpwaf_topic','wpwaf_forum');
$query->set('post__not_in', $search_blocked_ids );
$query->set('post_type', $search_allowed_types );
}
return $query;
}
```

**Following is what you need for this book:**
This book is targeted at WordPress developers and designers who want to develop quality web applications within a limited time frame and maximize their profits. A prior knowledge of basic web development and design is assumed.


## Get to Know the Author
**Rakhitha Nimesh Ratnayake**
 is a freelance web developer, writer, and open source enthusiast. He has over 7 years of experience in developing WordPress applications and plugins. He develops premium WordPress plugins for individual clients and the CodeCanyon marketplace. User Profiles Made Easy and WP Private Content Plus are the most popular plugins developed by him. Rakhitha is the creator of, where he shares his latest WordPress plugins. He is also a regular contributor to a number of popular websites, such as 1st webdesigner, the Tuts+ network, and the SitePoint network. Building Impressive Presentations with impress.js was his first book, which was published by Packt Publishing. He is also the author of the first and second editions of WordPress Web Application Development. In his spare time, he likes to watch cricket and spend time with his family.	


## Other books by the authors
[Building Impressive Presentations with impress.js](https://www.packtpub.com/web-development/building-impressive-presentations-impressjs?utm_source=github&utm_medium=repository&utm_campaign=9781849696487 )

[WordPress Web Application Development - Second Edition](https://www.packtpub.com/application-development/wordpress-web-application-development-second-edition?utm_source=github&utm_medium=repository&utm_campaign=9781782174394 )

[Wordpress Web Application Development - Third Edition](https://www.packtpub.com/application-development/wordpress-web-application-development-third-edition?utm_source=github&utm_medium=repository&utm_campaign=9781787126800 )

[Wordpress Development Quick Start Guide](https://www.packtpub.com/application-development/wordpress-development-quick-start-guide?utm_source=github&utm_medium=repository&utm_campaign=9781789342871 )



### Suggestions and Feedback
[Click here](https://docs.google.com/forms/d/e/1FAIpQLSdy7dATC6QmEL81FIUuymZ0Wy9vH1jHkvpY57OiMeKGqib_Ow/viewform) if you have any feedback or suggestions.


