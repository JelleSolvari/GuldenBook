<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class GuldenService
{
    public function __construct(private readonly Client $client)
    {
    }

    /**
     * @throws GuzzleException
     */
    private function getData(string $method, array $params = []): object
    {
        try {
            return json_decode($this->client->post('/', [
                'json' => [
                    'method' => $method,
                    'params' => $params,
                ],
            ])->getBody(), null, 512, JSON_THROW_ON_ERROR);
        } catch (GuzzleException $e) {
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * Checks if the GuldenD process is running.
     */
    public function running(): bool
    {
        try {
            $this->getUptime();

            return true;
        } catch (GuzzleException) {
            return false;
        }
    }

    /**
     * Returns the number of blocks in the longest blockchain.
     */
    public function getBlockCount(): int
    {
        return (int) $this->getData('getblockcount')->result;
    }

    /**
     * If verbosity is 0, returns a string that is serialized, hex-encoded data for block 'hash'.
     * If verbosity is 1, returns an Object with information about block <hash>.
     * If verbosity is 2, returns an Object with information about block <hash> and information about each transaction.
     */
    public function getBlock(string $hash, int $verbosity = 0): Collection
    {
        return collect($this->getData('getblock', [$hash, $verbosity])->result)->recursive();
    }

    /**
     * Returns hash of block in best-block-chain at height provided.
     */
    public function getBlockHash(int $height): string
    {
        return $this->getData('getblockhash', [$height])->result;
    }

    /**
     * Return the raw transaction data.
     * If verbose is 'true', returns an Object with information about 'txid'.
     * If verbose is 'false' or omitted, returns a string that is serialized, hex-encoded data for 'txid'.
     */
    public function getTransaction(string $txid, bool $verbose = false): Collection
    {
        return collect($this->getData('getrawtransaction', [$txid, $verbose])->result)->recursive();
    }

    /**
     * Returns the estimated network hashes per second based on the last n blocks.
     * Pass in $blocks to override # of blocks.
     * Pass in $height to estimate the network speed at the time when a certain block was found.
     */
    public function getNetworkHashrate(int $blocks = 120, int $height = -1): float
    {
        return $this->getData('getnetworkhashps', [$blocks, $height])->result;
    }

    /**
     * Returns the proof-of-work difficulty as a multiple of the minimum difficulty.
     */
    public function getDifficulty(): float
    {
        return $this->getData('getdifficulty')->result;
    }

    /**
     * Returns witness related network info for a given block.
     * When verbose is enabled returns additional statistics.
     */
    public function getWitnessInfo(string $blockSpecifier = 'tip', bool $verbose = false): Collection
    {
        $data = collect($this->getData('getwitnessinfo', [$blockSpecifier, $verbose])->result)->recursive();

        return ($data->count() === 1) ?
            $data->first() :
            $data;
    }

    public function getNetworkInfo(): Collection
    {
        return collect($this->getData('getnetworkinfo')->result)->recursive();
    }

    /**
     * Returns the total uptime of the server (in seconds).
     *
     *
     * @throws GuzzleException
     */
    public function getUptime(): int
    {
        return (int) $this->getData('uptime')->result;
    }

    public function getPeerInfo(): Collection
    {
        return collect($this->getData('getpeerinfo')->result)->recursive();
    }

    /**
     * @param  int  $blockHeight
     *                            TODO: implement reward halving every 4 (or so) years
     */
    public function getWitnessReward(int $blockHeight): float
    {
        if (config('gulden.testnet') !== null) {
            // testnet
            return ($blockHeight < 352200) ? 20.0 : 10.0;
        }

        return ($blockHeight < 1_400_000) ? 30.0 : 15.0;
    }
}
