<?php

namespace Monero;

class VotingState
{
    public $vote_id;

    public $block_start;

    public $block_end;

    public $blocks_counted;

    public $votes_option_yes;

    public $votes_option_no;

    public $votes_abstain;


    /**
     * VotingState constructor.
     *
     * @param $vote_id
     * @param $block_start
     * @param $block_end
     * @param $blocks_counted
     * @param $votes_option_yes
     * @param $votes_option_no
     * @param $votes_abstain
     */
    public function __construct($vote_id, $block_start, $block_end, $blocks_counted, $votes_option_yes, $votes_option_no, $votes_abstain)
    {
        $this->vote_id = $vote_id;
        $this->block_start = $block_start;
        $this->block_end = $block_end;
        $this->blocks_counted = $blocks_counted;
        $this->votes_option_yes = $votes_option_yes;
        $this->votes_option_no = $votes_option_no;
        $this->votes_abstain = $votes_abstain;
    }

}
