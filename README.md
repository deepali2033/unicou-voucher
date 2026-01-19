# CleanyCo - Professional Cleaning Services Laravel Application

This Laravel application is a complete conversion of the WordPress cleaning company website, maintaining the exact same design and UI/UX while leveraging Laravel's powerful framework.

## Features

- **Complete WordPress to Laravel Conversion**: Maintains identical design and functionality
- **Responsive Design**: Mobile-first approach with breakpoints matching the original
- **SEO Optimized**: Meta tags, structured data, and semantic HTML
- **Contact Forms**: Functional contact and quote request forms
- **Service Pages**: Comprehensive service descriptions and pricing
- **Blog System**: Ready for content management
- **Multi-location Support**: Service area pages for different cities

## Installation

### Prerequisites

- PHP 8.1 or higher
- Composer
- Node.js and NPM
- MySQL or PostgreSQL database

### Setup Instructions

1. **Clone or navigate to the project directory:**
   ```bash
   cd cleaning-company-laravel
   ```

2. **Install PHP dependencies:**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies:**
   ```bash
   npm install
   ```

4. **Create environment file:**
   ```bash
   copy .env.example .env
   ```

5. **Generate application key:**
   ```bash
   php artisan key:generate
   ```

6. **Configure your database in `.env` file:**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=cleaning_company
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

7. **Run database migrations (optional - for future blog/contact functionality):**
   ```bash
   php artisan migrate
   ```

8. **Build assets:**
   ```bash
   npm run build
   ```

9. **Start the development server:**
   ```bash
   php artisan serve
   ```

The application will be available at `http://localhost:8000`

## Project Structure

### Controllers

- **HomeController**: Handles home page and static pages
- **ServiceController**: Manages all service-related pages and service areas
- **AboutController**: About us, FAQ, privacy policy, terms of service
- **BlogController**: Blog functionality with categories and tags
- **ContactController**: Contact form processing
- **QuoteController**: Quote request form processing
- **PricingController**: Pricing information display
- **CareerController**: Job listings and career pages

### Views

- **layouts/app.blade.php**: Main layout template with header, navigation, and footer
- **home/**: Home page templates
- **services/**: Service pages templates
- **contact/**: Contact form template
- **about/**: About us related pages
- **blog/**: Blog templates
- **pricing/**: Pricing page templates
- **careers/**: Career pages templates

### Assets

- **public/css/**: Stylesheets including Elementor frontend CSS and custom styles
- **public/js/**: JavaScript files with VAMTAM theme functionality converted to vanilla JS
- **public/fonts/**: Custom fonts (Parkinsans, Outfit)
- **public/images/**: Image assets

## Routes

The application includes comprehensive routes for all pages:

- **Home**: `/`
- **Services**: `/services/*`
- **Service Areas**: `/service-areas/*`
- **About**: `/about-us/*`
- **Blog**: `/blog/*` (with date-based URLs)
- **Contact**: `/contact-us`
- **Quote**: `/free-quote`
- **Pricing**: `/pricing`
- **Careers**: `/careers/*`

## Design & Styling

### CSS Variables

The application uses CSS custom properties matching the original WordPress theme:

```css
:root {
    --e-global-color-vamtam_accent_1: #F2D701; /* Primary yellow */
    --e-global-color-vamtam_accent_2: #3CA200; /* Primary green */
    --e-global-color-vamtam_accent_3: #E8F5D3; /* Light green */
    --e-global-color-vamtam_accent_4: #F4F6F0; /* Light background */
    --e-global-color-vamtam_accent_5: #FFFFFF; /* White */
    --e-global-color-vamtam_accent_6: #000000; /* Black */
    /* ... */
}
```

### Typography

- **Primary Font**: Outfit (300, 400, 500, 600, 700)
- **Heading Font**: Parkinsans (400, 500)
- **Responsive typography** with breakpoints at 1024px and 767px

### JavaScript Functionality

- **Smooth scrolling** for anchor links
- **Sticky header** with scroll direction detection
- **Mobile menu** toggle functionality
- **Form validation** and submission handling
- **Animation on scroll** using Intersection Observer API
- **Responsive video** resizing

## Customization

### Adding New Pages

1. Create a new controller method
2. Add the route in `routes/web.php`
3. Create the corresponding Blade template
4. Update navigation in `layouts/app.blade.php`

### Styling Changes

- Modify CSS custom properties in `public/css/app.css`
- Add page-specific styles using `@push('styles')` in templates
- Use the existing utility classes for consistent spacing

### Content Updates

- Update contact information in `layouts/app.blade.php`
- Modify service descriptions in controller arrays
- Update meta tags and SEO information in templates

## SEO Features

- **Meta tags**: Title, description, keywords for each page
- **Open Graph tags**: Social media sharing optimization
- **Structured data**: JSON-LD schema for local business
- **Semantic HTML**: Proper heading hierarchy and landmarks
- **Mobile-friendly**: Responsive design with proper viewport settings

## Performance Optimizations

- **CSS minification**: Elementor frontend CSS is minified
- **Image optimization**: Placeholder for responsive images
- **Lazy loading**: Ready for implementation
- **Caching**: Laravel's built-in caching ready to configure

## Testing

### Manual Testing Checklist

1. **Navigation**: Test all menu links and mobile menu
2. **Forms**: Submit contact and quote forms with validation
3. **Responsive Design**: Test on desktop, tablet, and mobile
4. **Cross-browser**: Test in Chrome, Firefox, Safari, Edge
5. **Performance**: Check page load times and animations

### Automated Testing

Run Laravel's built-in tests:

```bash
php artisan test
```

## Deployment

### Production Checklist

1. Set `APP_ENV=production` in `.env`
2. Set `APP_DEBUG=false` in `.env`
3. Configure proper database credentials
4. Set up mail configuration for forms
5. Configure web server (Apache/Nginx)
6. Set up SSL certificate
7. Configure caching (`php artisan config:cache`)

### Environment Variables

Key environment variables to configure:

```env
APP_NAME="CleanyCo"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
```

## Support

For questions or issues with this Laravel conversion:

1. Check the Laravel documentation: https://laravel.com/docs
2. Review the original WordPress theme structure
3. Test individual components in isolation

## License

This project maintains the same licensing as the original WordPress theme while leveraging Laravel's open-source framework.

---

**Note**: This Laravel application maintains 100% design fidelity with the original WordPress site while providing a more maintainable and scalable codebase.
