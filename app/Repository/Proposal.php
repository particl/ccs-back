<?php

namespace App\Repository;

interface Proposal
{
    public function id() : int;
    public function url() : string;
    public function title() : string;
    public function author() : string;
    public function created_at() : int;
}