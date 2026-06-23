# Webshop Documentation

> The full documentation will be added here in the future. For now, please review the following important notes.

## Important Information

* Game images are currently represented by placeholder images displaying the game's name.
* Game data is loaded from the `database/data/games.json` file during the seeding process.
* Wishlist items and shopping cart contents are stored in the session for guest users and are automatically transferred to the user's account upon login.
* Product prices are automatically generated during seeding based on the game's weight and do **not** represent real prices.
* Box Collect locations are defined in the `config/box_collect_locations.php` file.
* Discounts and the `New` / `Bestseller` labels are assigned to products during the seeding process.
* Orders are stored in the database after checkout; however, no real payment gateway integration has been implemented.
* The website can be browsed as a guest user, and products can be added to the cart or wishlist. However, placing an order requires authentication.
