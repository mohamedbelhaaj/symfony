<?php
// src/Command/CleanupExpiredVerificationTokensCommand.php hhhh
namespace App\Command;

use App\Service\VerificationService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CleanupExpiredVerificationTokensCommand extends Command
{
    protected static $defaultName = 'app:cleanup-expired-verification-tokens';

    private $verificationService;

    public function __construct(VerificationService $verificationService)
    {
        $this->verificationService = $verificationService;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Cleans up expired verification tokens.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->verificationService->cleanupExpiredTokens();
        $output->writeln('Expired verification tokens cleaned up successfully.');

        return Command::SUCCESS;
    }
}
