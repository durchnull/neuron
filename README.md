# Neuron

Headless E-Commerce order processing software

_Table of contents_

1. Installation
2. Development
3. Documentation
4. Roadmap
    1. Tier 1
    2. Tier 2

# 1. Installation

```
ddev start

composer install

php artisan optimize

php migrate
```

# 2. Development

**Set up a demo merchant with sales channels and resources:**

```
php artisan merchant:demo 
```

# 3. Documentation

## Transactions

<u>Synchronous transaction</u>

@todo

<u>Asynchronous transaction</u>

@todo

# 4. Roadmap

## Tier 1

### Actions

- Gate return bool to use it for can-logic?
- User Actions
- Check Integrations CreateAction validations again

### Delete-Actions
- Action-Rule
  - Delete condition if it not used anywhere else?

### Coupon

- Combinable
    - Conditions on OrderRedeemCouponAction (ActionCouponEqual + )
- Usages (as a condition. like credit)

### Consequences

- Credit
    - UpdateRuleAction on order confirmation
    - Reduce credit on order accept
    - Increase credit on order refund

### Rules

- ExcludeOffers target
- Disable timed rules after ending with scheduler
- Condition Customer email equals

### Product

- Physical and non-physical types

### Integrations

- Service should generate jobs
- Integration: Shop
- Payment Provider
    - Mollie
        - cancelUrl
        - consumerDateOfBirth
        - Mollie components for credit card
    - AmazonPay (https://github.com/amzn/amazon-pay-api-sdk-php)
      - AmazonPayButton
        - Estimated amount is inclusive or exclusive shipping cost?
- Inventory
    - Billbee (billbee/custom-shop-api require php 5.6.* || ^7.0 -> your php version (8.2.3))
    - Weclapp
    - Shop as Inventory
    - Sync stock
- Check usage of http post() vs postJson()

### VatRates

### Payment

- Invoice (inform inventory or other integration)
- SEPA (OrderPlaceAction, orders.payment_information attribute)
- Add card_token to neuron payment creditcard

### Transactions

- Update transactions when order status changes (cancel, refund, ...)

### Customer

- Reset new customer flag on order cancel/refund or failed transaction (order reopening)
- Type, organization or person ?

### Condition

- Exceptions on property and comparison mismatches
- Not combinable with coupon

### Address

- Postbox
- Primary address assignment

### Database

- Encrypted user data?

### Commands

- NeuronAction: test all variations

### Users

- Improve

### Authentication

- SalesChannel
    - Domains

### Admin

- Setup wizard

### API

- Responses
    - 400, Bad Request, Invalid product ID or quantity
    - 403, Forbidden, You do not have permission to modify this order
    - 409, Conflict, The order is currently being processed and cannot be modified
    - 422, Unprocessable Entity, Insufficient stock for this product
  - responses
    - data: can contain the cart or other data
    - code: a string code indicating the error
- Requests
    - Show and IndexRequest generalization
- Filtered validation errors for customers
- Order
    - Token and domain matching? Allowed domains?
    - Place route, use Orderservice for checking all the things again?
- SalesChannel
    - Return token only on creation?
- Merchant
  - Should merchants have an API?

### Validation

- Required target ids in actions?
- Configuration product needs to be enabled
- Free payment cant be disabled
- Customer cant select free payment

### Refactoring

- Reconsider Target serialization
- Naming totals vs summary
- Rename item total_amount to total?
- Transaction 'payment' naming
- 'payment' can falsely be replaced by 'checkout'
- All Triggers
- Refactor tests function names
- Refactor 429 http status code

### Documentation

### App

- Integration Stubs Generator
- PCI-DSS SAQ-A

### Tests

- Rules sorting (position)
- Rules presets
- Cascading on delete
- Various rules
- Customer
- Addresses
- Integration
- OrderUpdatePaymentAction/OrderUpdateShippingAction
- Bundle configuration validations
- distinct bundle configuration adds distinct order items
- Mixed consequence targets
- Customer update with shipping country that does not match any shipping
- Shipping and item discounts are removed when their conditions fails after they have previously succeeded
- Shipping address can be updated only if a shipping with the matching country code exist
- Coupon and rules with add-item consequences of disabled/out of stock products
- products with and without vat rates
- Sales Channel domain authentication
- Cart with items of rules, then change rule, check cart updates
- Remove items with deactivated products
- ExcludeOffers target
- ProductPrices
- Remove deactivated coupons
- Coupon not redeemable
- Order place additional payment information
- Payment changes from Proxy payment like amazon to Free after coupon redemption

## Tier 2

### App
- Caching

### Registration

### Shipping

- Smart Defaults: Pre-fill fields like shipping information based on the customer's IP address or previous purchases
  when possible. This reduces the amount of typing customers need to do.
- Auto-Detect Location: Use geolocation to automatically detect the user's location and suggest or pre-select shipping
  options accordingly.

### Address

- Address Verification: Implement an address verification system to minimize errors and reduce the need for customers to
  manually correct their addresses.

### Order

- Order refund page
- Order summary page with tracking information
- Product version updates in items with delay?

### Cart

- Inactivity emailing
- Inactivity closing
- Inactivity coupon

### Stock

- Stock warnings

### Actions

- Allow Changing product type from product to bundle? (Checking others bundle configuration)

### Bundles

- Can Product configurations with disabled configurations be stored initially? 
### Integrations

- Statistics Module
- Handle multiple same integrations
- Mail
    - Mailchimp
- Payment Provider
    - Mollie
        - PayPal Express
        - Apply Pay button with wallets
    - PostFinance (https://github.com/pfpayments/php-sdk, https://checkout.postfinance.ch/doc/api/web-service)
    - PayPal (https://developer.paypal.com/docs/api/orders/v2/)

### Payments
- There always needs to be a default payment

### Condition

- Automatic condition naming

### Refactoring

- Better factory data
