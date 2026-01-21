# Blog CMS (PHP/MySQL)

This project is a starter scaffold for a Blog/CMS system using PHP 7.4+, MySQL 8+, and vanilla JavaScript. It follows the folder structure and technical requirements you specified (procedural PHP, prepared statements, utf8mb4, XAMPP-compatible).

## Current Status

The following items are already created in `C:\Users\HP\Documents\blog_cms`:

- Folder structure for the app (see below)
- `config/config.php`
  - Site constants
  - Environment defaults
  - `BASE_URL`, uploads paths, and basic runtime settings
- `config/database.php`
  - `mysqli` connection helper
  - Prepared-statement query helpers
  - Fetch helpers and error handling

## Folder Structure

```
blog_cms/

 config/
    database.php          # Database connection & query helpers
    config.php            # Site configuration & constants

 includes/
    header.php            # HTML header with SEO meta tags
    footer.php            # HTML footer
    functions.php         # Core PHP functions library

 admin/
    index.php             # Admin dashboard with statistics
    posts.php             # Post creation/editing interface
    comments.php          # Comment moderation panel
    categories.php        # Category management
    users.php             # User management (admin only)
    profile.php           # User profile editing

 uploads/
    avatars/
       default-avatar.png # Default user avatar (200x200px)
    posts/
        .htaccess         # Security: prevent PHP execution

 assets/
    css/
       style.css          # Main stylesheet
       admin.css          # Admin-specific styles (optional)
    js/
       main.js            # Frontend JavaScript
       admin.js           # Admin panel JavaScript
    images/
        logo.png          # Site logo

 index.php                 # Homepage with post listing
 post.php                  # Single post view with comments
 category.php              # Category archive page
 tag.php                   # Tag archive page
 author.php                # Author profile & posts
 search.php                # Search results page
 login.php                 # User login form
 register.php              # User registration form
 logout.php                # Logout handler
 rss.php                   # RSS feed generator
 sitemap.xml.php           # XML sitemap for SEO
 .htaccess                 # URL rewriting & security rules
```

## Next Steps (not implemented yet)

- Implement database schema and migrations
- Add core PHP includes: `includes/header.php`, `includes/footer.php`, `includes/functions.php`
- Build public pages (`index.php`, `post.php`, etc.)
- Build admin pages (`admin/*`)
- Add authentication and authorization
- Add TinyMCE integration for post editor
- Add RSS, sitemap, and URL rewriting
- Add assets (`assets/css`, `assets/js`, `assets/images`) and default avatar

## Notes

- Update database credentials in `config/config.php` for your environment.
- The system assumes `utf8mb4` for full Unicode support.
