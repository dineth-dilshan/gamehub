# The Gamer's HUB - PHP Authentication & E-Commerce System

## Complete Setup Guide

This system provides full user authentication, session management, cart management, and wishlist functionality for your gaming store.

---

## ✅ FILES CREATED

### PHP Backend Files
1. **config.php** - Database connection configuration
2. **register_handler.php** - User registration with validation
3. **login_handler.php** - User login with password verification
4. **logout.php** - User logout and session destruction
5. **add_to_cart.php** - Add items to user cart
6. **add_to_wishlist.php** - Add items to user wishlist
7. **get_cart.php** - Retrieve cart items for logged-in user
8. **get_wishlist.php** - Retrieve wishlist items for logged-in user
9. **remove_from_cart.php** - Remove items from cart
10. **remove_from_wishlist.php** - Remove items from wishlist
11. **clear_cart.php** - Clear entire cart
12. **database_setup.sql** - Database structure

### Updated HTML Files
1. **register.html** - Registration form with PHP integration
2. **8.Logintab.html** - Login form with PHP integration
3. **storehome.html** - Store homepage with avatar dropdown & session check
4. **cart.html** - Shopping cart with database integration
5. **wishlist.html** - Wishlist with database integration
6. **account.html** - New! User account/profile page

---

## 🔧 INSTALLATION STEPS

### Step 1: Set Up Local Server
You need a local server with PHP and MySQL. Install one of these:
- **XAMPP** (Windows/Mac/Linux) - https://www.apachefriends.org/
- **WAMP** (Windows) - https://www.wampserver.com/
- **MAMP** (Mac) - https://www.mamp.info/

### Step 2: Extract Your Project
Place your project in the server's htdocs folder:
- XAMPP: `C:\xampp\htdocs\gamers-hub\`
- WAMP: `C:\wamp\www\gamers-hub\`
- MAMP: `/Applications/MAMP/htdocs/gamers-hub/`

### Step 3: Create Database

1. Open **phpMyAdmin** (usually at `http://localhost/phpmyadmin`)
2. Click "New" and create a database named `gamers_hub`
3. Select the `gamers_hub` database
4. Click "Import" and upload the `database_setup.sql` file
5. Click "Go" to execute

### Step 4: Configure Database Connection

Edit `config.php` and update the following if needed:
```php
define('DB_HOST', 'localhost');     // Your host
define('DB_USER', 'root');          // Your MySQL username
define('DB_PASS', '');              // Your MySQL password
define('DB_NAME', 'gamers_hub');    // Database name
```

### Step 5: Start Your Server
- **XAMPP**: Open XAMPP Control Panel and click "Start" for Apache & MySQL
- **WAMP**: Click the WAMP icon and select "Start All Services"
- **MAMP**: Open MAMP and click "Start Servers"

### Step 6: Access Your Site
Open your browser and go to:
```
http://localhost/gamers-hub/storehome.html
```

---

## 📋 USER FLOW

### Registration
1. User goes to `register.html`
2. Fills in First Name, Last Name, Email, Username, and Password
3. System validates:
   - Username must be at least 3 characters
   - Email must be valid
   - Password must be at least 6 characters
   - **Username must be unique** (shows "Username already exists")
   - **Email must be unique** (shows "Email already exists")
4. On success: Auto-redirects to login page

### Login
1. User goes to `8.Logintab.html`
2. Enters Username and Password
3. System validates:
   - **If username/password wrong**: Shows "Wrong username or password"
   - **If correct**: Sets session and redirects to `storehome.html`
4. User sees avatar in top-right corner (only when logged in)

### Avatar & Account Menu
1. Logged-in user sees avatar in navigation bar (top-right)
2. Click avatar to see dropdown menu with options:
   - My Account
   - My Cart
   - Wishlist
   - Logout
3. Each user's data is separate (different database records)

### Cart Management
1. Only logged-in users can add items to cart
2. Non-logged-in users get redirected to login
3. Cart items persist in database (not lost on browser close)
4. Each user has their own separate cart
5. Can remove items or clear entire cart
6. Cart displays total price

### Wishlist Management
1. Add items to wishlist (logged-in users only)
2. Move items from wishlist to cart
3. Remove items from wishlist
4. Each user has their own separate wishlist

---

## 🔐 SECURITY FEATURES

✅ **Password Hashing** - Uses PHP's `password_hash()` for secure storage
✅ **Session Management** - User data stored in $_SESSION
✅ **SQL Injection Protection** - Uses prepared statements with `bind_param()`
✅ **XSS Protection** - Uses `htmlspecialchars()` for output
✅ **User Isolation** - Each user can only see their own data
✅ **Login Validation** - Wrong credentials don't reveal if account exists

---

## 📊 DATABASE STRUCTURE

### Users Table
```sql
- id: Auto-increment primary key
- first_name: User's first name
- last_name: User's last name
- email: Unique email address
- username: Unique username
- password: Hashed password
- avatar: Avatar image URL
- created_at: Registration timestamp
```

### Cart Table
```sql
- id: Auto-increment primary key
- user_id: Links to users table
- product_id: Product identifier
- product_name: Product name
- product_price: Price per item
- quantity: Number of items
- added_at: When added to cart
```

### Wishlist Table
```sql
- id: Auto-increment primary key
- user_id: Links to users table
- product_id: Product identifier
- product_name: Product name
- product_price: Product price
- added_at: When added to wishlist
```

---

## 🎯 TESTING THE SYSTEM

### Test Registration
1. Go to `register.html`
2. Try registering with existing username → Should show error
3. Try registering with invalid email → Should show error
4. Register successfully with new username
5. Should redirect to login page

### Test Login
1. Try wrong password → Shows "Wrong username or password"
2. Try non-existent username → Shows "Wrong username or password"
3. Login with correct credentials → Redirects to store homepage
4. Avatar appears in top-right navigation

### Test Cart
1. Logged in, click "Add to Cart" on a product
2. Go to cart page → Item appears
3. Try removing item → Item disappears
4. Logout and login again → Cart still has items (saved in database!)

### Test Wishlist
1. Add items to wishlist
2. Go to wishlist page → Items appear
3. Move to cart → Item appears in cart
4. Logout and login again → Wishlist preserved in database!

---

## ⚙️ CUSTOMIZATION

### Change Avatar Service
In `register_handler.php`, line with `ui-avatars.com`:
```php
$avatar = "https://ui-avatars.com/api/?name=" . urlencode($first_name . " " . $last_name) . "&background=a855f7&color=fff";
```

You can use:
- **Gravatar**: `https://www.gravatar.com/avatar/` + md5(email)
- **Custom images**: Upload to your server
- **Initials**: Generate custom initials

### Add More Products
Update product cards in `storehome.html` with unique `product_id` and `product_name`.

### Change Styling
All CSS is within the HTML files. Modify color schemes, fonts, and layouts as needed.

---

## 🐛 TROUBLESHOOTING

### "Connection failed"
- Ensure MySQL server is running
- Check `config.php` has correct credentials
- Database `gamers_hub` exists

### "User already exists" when registering
- That username is taken
- Try a different username

### Cart items not saving
- User not logged in
- Ensure session is started
- Check database user/cart table

### Avatar not showing
- User not logged in (needs session)
- Check `$_SESSION['avatar']` is set
- Try clearing browser cache

### 404 errors on PHP files
- Ensure `.php` files are in project root (same folder as HTML files)
- Check file names match exactly
- Ensure server supports PHP

---

## 📝 IMPORTANT NOTES

1. **Passwords**: Users should use strong passwords (min 6 chars) but encourage longer ones
2. **Email**: Currently, emails are stored but not verified. Add verification in future
3. **Avatar**: Auto-generated from names, but can be changed to user uploads
4. **Checkout**: Currently placeholder. Implement payment gateway (Stripe, PayPal, etc.)
5. **Security**: For production, add HTTPS, CSRF tokens, rate limiting

---

## 🚀 NEXT STEPS

1. ✅ Set up database (follow Step 3)
2. ✅ Configure PHP settings (follow Step 4)
3. ✅ Test all features
4. 📧 Add email verification (optional)
5. 💳 Add payment processing
6. 📦 Add order management
7. 🔍 Add search & filters
8. ⭐ Add product ratings/reviews

---

## 📞 FILE REFERENCES

- **For registration validation**: See `register_handler.php`
- **For login logic**: See `login_handler.php`
- **For cart operations**: See `add_to_cart.php`, `get_cart.php`
- **For wishlist operations**: See `add_to_wishlist.php`, `get_wishlist.php`
- **For session handling**: See `storehome.html` PHP section at top

---

**Your complete e-commerce authentication system is ready! Enjoy! 🎮**
