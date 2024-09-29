<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->delete();
        DB::table('permissions')->delete();
        DB::table('role_has_permissions')->delete();
        DB::table('model_has_permissions')->delete();
        DB::table('model_has_roles')->delete();
        $adminRole = Role::create(['name' => 'admin']);
        $managerRole = Role::create(['name' => 'manager']);
        $vendorRole = Role::create(['name' => 'vendor']);
        $customerSupportRole = Role::create(['name' => 'customer support']);
        $shippingStaffRole = Role::create(['name' => 'shipping staff']);
        $marketingStaffRole = Role::create(['name' => 'marketing staff']);
        $customerRole = Role::create(['name' => 'customer']);

        /**
         * Manage Users: Add, update, delete, activate, deactivate users (admins, customers, sellers, etc.).
         * Manage Roles & Permissions: Assign roles and permissions to users.
         * Manage Products: Add, update, delete, approve, and publish products.
         * Manage Orders: View, update, and delete orders.
         * View & Manage Payments: Review, process, and approve payments and refunds.
         * Manage Categories & Tags: Create, update, and delete categories, tags, or other product taxonomies.
         * Manage Discounts & Coupons: Create and manage discount codes and offers.
         * Manage Settings: Configure site-wide settings like payment gateways, shipping options, taxes, etc.
         * Manage Inventory: Oversee stock levels, reorders, and suppliers.
         * View & Manage Reports: View sales, customer, and inventory reports.
         * Manage Customer Support: Oversee customer service representatives, handle escalations.
         * Manage Marketing: Oversee promotional campaigns, email marketing, banners, and pop-ups.
         */
        $adminPermissions = [
            'manage categories',
            'add user', 'edit user', 'delete user', 'show user',
            'add role to user', 'remove role from user',
            'add permissions to user', 'remove permissions from user',
            'add product', 'edit product', 'delete product', 'approve product', 'publish product',
            'edit order', 'delete order', 'show order',
            'edit payment', 'show payment',
            'view report',
            'manage coupons',
            'add settings', 'edit settings', 'delete settings', 'show settings',
            'add inventory', 'edit inventory', 'delete inventory', 'show inventory',
        ];
        /**
         * View Reports: Sales, customer, and inventory reports.
         * Manage Orders: View, update, and process customer orders.
         * Manage Products: Add, update, and delete products.
         * Manage Inventory: Update stock levels and restock items.
         * Manage Discounts & Coupons: Add and manage promotional offers and discounts.
         * Manage Marketing Campaigns: Launch and monitor marketing campaigns.
         * Handle Customer Queries: Respond to customer issues related to orders, payments, or returns.
         */
        $managerPermissions = [
            'add product', 'edit product', 'delete product', 'approve product', 'publish product',
            'edit order', 'delete order', 'show order',
            'edit payment', 'show payment',
            'view report',
            'manage coupons',
            'add inventory', 'edit inventory', 'delete inventory', 'show inventory',
        ];
        /**
         * Manage Own Products: Add, update, delete, and manage the visibility of their own products.
         * View & Manage Orders: View orders for their products, process shipments, and mark orders as completed.
         * View Reports: View reports on their own sales and performance.
         * Manage Inventory: Track stock levels for their own products.
         * Handle Customer Queries: Respond to customer inquiries related to their products.
         */
        $vendorPermissions = [
            'add product', 'edit product', 'delete product',
            'edit order', 'delete order', 'show order',
            'edit payment', 'show payment',
            'view report',
            'show inventory',
        ];
        /**
         * View Orders: View customer orders to assist with issues like returns or tracking.
         * Manage Returns/Refunds: Process customer requests for returns or refunds.
         * View Customer Information: Access customer details to help with issues.
         * Manage Customer Queries: Respond to customer support tickets or live chats.
         * View Products: Access product details for reference during customer interactions.
         * Manage Customer Accounts: Help customers reset passwords or update account details.
         */
        $customerSupportPermissions = [
            'show user',
            'show order',
            'edit payment', 'show payment',
        ];
        /**
         * View Orders: View and manage orders that are ready for shipment.
         * Update Shipping Status: Mark orders as shipped, in transit, or delivered.
         * Manage Shipping Details: Update or modify shipping addresses if requested by the customer.
         * Print Shipping Labels: Generate and print shipping labels for orders.
         */
        $shippingStaffPermissions = [
            'show user',
            'edit order', 'show order',
        ];
        /**
         * Manage Discounts & Coupons: Create, update, and delete discount codes and promotional offers.
         * Manage Email Campaigns: Create and manage email newsletters and promotional emails.
         * Manage Advertisements: Set up banners, sliders, and ad campaigns on the site.
         * View Reports: Analyze marketing campaign effectiveness via reports.
         * Manage SEO: Edit product metadata, tags, and categories for SEO purposes.
         */
        $marketingStaffPermissions = [
            'manage categories',
            'show payment',
            'view report',
            'manage coupons',
        ];
        /**
         * View & Purchase Products: Browse, add to cart, and purchase products.
         * Manage Own Orders: View their order history, track shipments, request refunds or returns.
         * Manage Own Profile: Update personal information, shipping addresses, payment methods.
         * Submit Reviews: Submit product reviews and ratings.
         * Manage Wishlists: Create and update wishlists for future purchases.
         */
        $customerPermissions = [
            'add to cart', 'buy product',
            'edit order', 'show order',
        ];

        $permissions = array_unique(array_merge($adminPermissions, $managerPermissions, $vendorPermissions,
            $customerSupportPermissions, $shippingStaffPermissions, $marketingStaffPermissions, $customerPermissions));
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $adminRole->syncPermissions($adminPermissions);
        $managerRole->syncPermissions($managerPermissions);
        $vendorRole->syncPermissions($vendorPermissions);
        $customerSupportRole->syncPermissions($customerSupportPermissions);
        $shippingStaffRole->syncPermissions($shippingStaffPermissions);
        $marketingStaffRole->syncPermissions($marketingStaffPermissions);
        $customerRole->syncPermissions($customerPermissions);

        User::where(['email' => 'admin_Admin@yahoo.com'])->forcedelete();
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin_Admin@yahoo.com',
            'email_verified_at' => now(),
            'password' => Hash::make('AdminPassword'),
            'role' => 'admin'
        ]);
        $admin->assignRole($adminRole);
    }
}
