<?php
class HomeController extends Controller
{
    public function index(): void
    {
        $this->render('home', [
            'trades' => Trade::all_(),
            'counts' => Worker::countByTrade(),
        ]);
    }
}
