<?php

namespace App\Console\Commands\Engine\Merchant;

use App\Actions\Engine\Merchant\MerchantCreateAction;
use App\Enums\TriggerEnum;
use App\Exceptions\Order\PolicyException;
use App\Models\Engine\Merchant;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Validation\ValidationException;

use function Laravel\Prompts\info;
use function Laravel\Prompts\table;
use function Laravel\Prompts\text;

class MerchantCreate extends Command
{
    protected $signature = 'merchant:create';

    protected $description = 'Create a merchant';

    /**
     * @return int
     * @throws PolicyException
     * @throws ValidationException
     * @throws Exception
     */
    public function handle(): int
    {
        $merchantCreateAction = new MerchantCreateAction(new Merchant(), [
            'name' => text(
                label: 'Merchant name',
                required: true
            )
        ], TriggerEnum::App);

        $merchantCreateAction->trigger();

        /** @var Merchant $merchant */
        $merchant = $merchantCreateAction->target();

        table(
            ['Id', 'Name', 'Token'],
            [
                [
                    'Id' => $merchant->id,
                    'Name' => $merchant->name,
                    'Token' => $merchant->token,
                ],
            ]
        );

        info('✔︎ Merchant created');

        return Command::SUCCESS;
    }
}
