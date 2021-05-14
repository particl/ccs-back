<?php

namespace App\Console\Commands;

use App\Coin\CoinAuto;
use App\Project;
use Illuminate\Console\Command;
use Monero\VotingState;

class VoteNotify extends Command
{
    private $coin;
    private $wallet;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vote:notify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks the blockchain for votes';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->coin = CoinAuto::newCoin();
        $this->wallet = $this->coin->newWallet();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $blockheight = $this->wallet->blockHeight();
        if ($blockheight < 1) {
            $this->error('failed to fetch blockchain height');
            return;
        }

    
        // The blockchain may re-org to a depth of 1000 blocks
        // Which means the only block that we're definete of is 1000 blocks ago.
        $current_height_definite = $blockheight - 1000;
      
        // Retrieve the state for all active votes 
        $active_votes = Vote::where('finished', '==', 0)->each(function ($vote_row) {
            // Get the vote from particld
            $vote_id = $vote_row->id;
            $block_start = $vote_row->block_start;
            $block_end = $vote_row->block_end;
            $vote = $this->wallet->checkIncomingVotes($vote_id, $block_start, $block_end);

            // Process the voting results in the database
            processVote($vote, $current_height_definite);
        });

    }

    /**
     * @param VotingState $vote
     * @param $current_height_definite
     *
     * @return null|void
     */
    public function processVote(VotingState $vote, $current_height_definite)
    {
        $vote_db = Vote::where('id', $vote->vote_id)->first();
        $vote_db->blocks_yes = $vote->votes_option_yes;
        $vote_db->blocks_no = $vote->votes_option_no;
        $vote_db->blocks_abstain = $vote->votes_abstain;

        // If we passed the point of the definite unreorganizable height, then we consider the vote finished.
        if($vote_db->block_height_end >= $current_height_definite) {
            $vote_db->finished = 1;
        }
        // TODO: update the project network vote state as well?

        $vote_db->save();
        return;
    }
}
