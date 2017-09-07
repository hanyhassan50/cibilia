AdvanceOverMax()
    {
        $bar = new ProgressBar($output = $this->getOutputStream(), 10);
        $bar->setProgress(9);
        $bar->advance();
        $bar->advance();

        rewind($output->getStream());
        $this->assertEquals(
            $this->generateOutput('  9/10 [=========================>--]  90%').
            $this->generateOutput(' 10/10 [============================] 100%').
            $this->generateOutput(' 11/11 [============================] 100%'),
            stream_get_contents($output->getStream())
        );
    }

    public function testCustomizations()
    {
        $bar = new ProgressBar($output = $this->getOutputStream(), 10);
        $bar->setBarWidth(10);
        $bar->setBarCharacter('_');
        $bar->setEmptyBarCharacter(' ');
        $bar->setProgressCharacter('/');
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%%');
        $bar->start();
        $bar->advance();

        rewind($output->getStream());
        $this->assertEquals(
            $this->generateOutput('  0/10 [/         ]   0%').
            $this->generateOutput('  1/10 [_/        ]  10%'),
            stream_