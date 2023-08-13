<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            MerchantSeeder::class,
            SalesChannelSeeder::class,
            ShippingSeeder::class,
            PaymentSeeder::class,
            ProductSeeder::class,
            StockSeeder::class,
            OrderSeeder::class,
            OrderItemSeeder::class,
            CustomerSeeder::class,
            CouponSeeder::class,
            ConditionSeeder::class,
            RuleSeeder::class,
            TransactionSeeder::class,
            NeuronInventorySeeder::class,
            BillbeeSeeder::class,
            WeclappSeeder::class,
            AmazonPaySeeder::class,
            PostFinanceSeeder::class,
            MollieSeeder::class,
            KlicktippSeeder::class,
            MailgunSeeder::class,
            NeuronPaymentSeeder::class,
            ConditionSeeder::class,
            ActionRuleSeeder::class,
            CartRuleSeeder::class,
            VatSeeder::class,
            ProductPriceSeeder::class,
            PaypalSeeder::class
        ]);
    }
}
