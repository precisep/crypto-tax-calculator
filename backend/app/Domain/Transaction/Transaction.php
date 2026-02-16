<?php

namespace App\Domain\Transaction;

use Carbon\Carbon;

class Transaction
{
    public function __construct(
        public int $id,
        public string $coin,
        public string $type,
        public float $amount,
        public float $price,
        public Carbon $date,
        public float $fee = 0.0,  
        public ?string $feeCoin = null,
        public string $wallet = 'default',
        public ?string $fromCoin = null,
        public ?string $toCoin = null,
        public ?string $fromWallet = null,
        public ?string $toWallet = null,
        public ?string $description = null
    ) {
        \Log::info("Transaction created", [
            'id' => $id,
            'coin' => $coin,
            'type' => $type,
            'fee_value' => $fee,
            'fee_as_percentage' => ($fee * 100) . '%',
        ]);
    }
    
    public static function fromArray(array $data): self
    {
        \Log::info("Creating Transaction from array", ['data' => $data]);
        
        
        $fee = $data['fee'] ?? 0.0;
        
        \Log::info("Raw fee value from JSON: {$fee} (type: " . gettype($fee) . ")");
        
       
        $fee = floatval($fee);
        
     
        if ($fee > 0 && $fee < 1) {
            $fee = $fee / 100;
            \Log::info("Converted fee from percentage to decimal: {$fee}");
        }
        
        \Log::info("Final fee value (as decimal): {$fee}");

        return new self(
            id: $data['id'] ?? 0,
            coin: $data['coin'],
            type: $data['type'],
            amount: (float) $data['amount'],
            price: (float) $data['price'],
            date: Carbon::parse($data['date']),
            fee: $fee,
            feeCoin: $data['fee_coin'] ?? $data['feeCoin'] ?? null,
            wallet: $data['wallet'] ?? 'default',
            fromCoin: $data['from_coin'] ?? $data['fromCoin'] ?? null,
            toCoin: $data['to_coin'] ?? $data['toCoin'] ?? null,
            fromWallet: $data['from_wallet'] ?? $data['fromWallet'] ?? null,
            toWallet: $data['to_wallet'] ?? $data['toWallet'] ?? null,
            description: $data['description'] ?? null
        );
    }
    
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'coin' => $this->coin,
            'type' => $this->type,
            'amount' => $this->amount,
            'price' => $this->price,
            'date' => $this->date->format('Y-m-d'),
            'fee' => $this->fee,
            'fee_coin' => $this->feeCoin,
            'wallet' => $this->wallet,
            'from_coin' => $this->fromCoin,
            'to_coin' => $this->toCoin,
            'from_wallet' => $this->fromWallet,
            'to_wallet' => $this->toWallet,
            'description' => $this->description
        ];
    }
    
    public function getFeePercentage(): float
    {
        return $this->fee;
    }
    
    public function calculateFeeAmount(float $proceeds): float
    {
        $feeAmount = $proceeds * $this->fee;
        \Log::info("Calculating fee: {$proceeds} * {$this->fee} = {$feeAmount}");
        return $feeAmount;
    }
    
    // Magic getter for snake_case compatibility
    public function __get(string $name): mixed
    {
        $propertyMap = [
            'fee_coin' => 'feeCoin',
            'fee_rate' => 'fee',
            'from_coin' => 'fromCoin',
            'to_coin' => 'toCoin',
            'from_wallet' => 'fromWallet',
            'to_wallet' => 'toWallet',
        ];
        
        if (array_key_exists($name, $propertyMap)) {
            return $this->{$propertyMap[$name]};
        }
        
        $camelName = str_replace('_', '', ucwords($name, '_'));
        $camelName = lcfirst($camelName);
        
        if (property_exists($this, $camelName)) {
            return $this->{$camelName};
        }
        
        return null;
    }
    
    public function __isset(string $name): bool
    {
        $propertyMap = [
            'fee_coin' => 'feeCoin',
            'fee_rate' => 'fee',
            'from_coin' => 'fromCoin',
            'to_coin' => 'toCoin',
            'from_wallet' => 'fromWallet',
            'to_wallet' => 'toWallet',
        ];
        
        if (array_key_exists($name, $propertyMap)) {
            return isset($this->{$propertyMap[$name]});
        }
        
        $camelName = str_replace('_', '', ucwords($name, '_'));
        $camelName = lcfirst($camelName);
        
        return property_exists($this, $camelName);
    }
}