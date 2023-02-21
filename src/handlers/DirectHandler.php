<?php

namespace nsusoft\dadata\handlers;

use nsusoft\dadata\adapters\methods\FindByIdAdapter;
use nsusoft\dadata\adapters\methods\SuggestAdapter;
use nsusoft\dadata\api\Client;
use nsusoft\dadata\dto\DtoInterface;
use nsusoft\dadata\factories\DirectFactory;
use nsusoft\dadata\factories\FactoryInterface;
use nsusoft\dadata\Module;
use nsusoft\dadata\types\enums\CleanType;
use nsusoft\dadata\types\enums\FindByIdType;
use nsusoft\dadata\types\enums\SuggestType;
use yii\base\InvalidCallException;

class DirectHandler extends BaseHandler
{
    /**
     * @inheritDoc
     */
    public function clean(string $type, string $value): ?DtoInterface
    {
        if (!CleanType::exists($type)) {
            throw new InvalidCallException(Module::t('main', 'Invalid clean type.'));
        }

        $adapter = $this->createFactory()->createClean($type);
        $adapter->setSource($this->createClient()->clean($type, $value));

        return $adapter->populate();
    }

    /**
     * @inheritDoc
     */
    public function suggest(string $type, string $value, array $options = []): array
    {
        if (!SuggestType::exists($type)) {
            throw new InvalidCallException(Module::t('main', 'Invalid suggest type.'));
        }

        $method = new SuggestAdapter([
            'client' => $this->createClient(),
            'type' => $type,
            'value' => $value,
            'options' => $options,
        ]);

        $adapter = $this->createFactory()->createSuggest($type);
        $adapter->setSource($method->call());

        return $adapter->populate();
    }

    /**
     * @inheritDoc
     */
    public function findById(string $type, string $value, array $options = []): ?DtoInterface
    {
        if (!FindByIdType::exists($type)) {
            throw new InvalidCallException(Module::t('main', 'Invalid suggest type.'));
        }

        $method = new FindByIdAdapter([
            'client' => $this->createClient(),
            'type' => $type,
            'value' => $value,
            'options' => $options,
        ]);

        $adapter = $this->createFactory()->createFindById($type);
        $adapter->setSource($method->call());

        return $adapter->populate()[0];
    }

    /**
     * @return DirectFactory
     */
    protected function createFactory(): FactoryInterface
    {
        return new DirectFactory();
    }

    /**
     * @return Client
     */
    private function createClient(): Client
    {
        return new Client();
    }
}