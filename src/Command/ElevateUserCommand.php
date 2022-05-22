<?php

namespace App\Command;

use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:elevate-user',
    description: 'Make user an admin',
)]
class ElevateUserCommand extends Command
{
    public function __construct(
        private readonly UserRepository $userRepository
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('user', InputArgument::OPTIONAL, 'user email');
    }

    protected function execute(
        InputInterface  $input,
        OutputInterface $output
    ): int
    {
        $io = new SymfonyStyle($input, $output);
        $id = $input->getArgument('user');

        $user = $this->userRepository->findOneBy(['email' => $id]);

        $roles = array_unique(array_merge($user->getRoles(), ['ROLE_ADMIN']));
        sort($roles);
        $user->setRoles($roles);
        $this->userRepository->add($user, true);

        if ($id) {
            $io->note(sprintf('You passed an user: %s', $id));
        }

        return Command::SUCCESS;
    }
}
