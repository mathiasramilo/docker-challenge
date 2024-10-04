<?php

namespace App;

use GuzzleHttp\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ShowMovieCommand extends Command
{
	protected static $defaultName = 'show';

	protected function configure()
	{
		$this->setDescription('Fetches information about a movie from OMDB.')
			->addArgument('title', InputArgument::REQUIRED, 'The title of the movie.')
			->addOption('fullPlot', null, InputOption::VALUE_NONE, 'Muestra la trama completa de la película.');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$title = $input->getArgument('title');
		$fullPlot = $input->getOption('fullPlot') ? 'full' : 'short';

		// Create a Guzzle client
		$client = new Client();

		// Access the API key from the environment
		$apiKey = $_ENV['OMDB_API_KEY'];

		// Make the API request
		$response = $client->request('GET', 'http://www.omdbapi.com/', [
			'query' => [
				't' => $title,
				'apiKey' => $apiKey,
				'plot' => $fullPlot
			]
		]);

		$movieData = json_decode($response->getBody(), true);

		// Check if the movie was found
		if (isset($movieData['Error'])) {
			$output->writeln("<error>{$movieData['Error']}</error>");
			return Command::FAILURE;
		}

		// Display movie title - year
		$output->writeln("<info>{$movieData['Title']} - {$movieData['Year']}</info>");

		// Create a table to display movie information
		$table = new Table($output);
		$rows = [];

		foreach ($movieData as $key => $value) {
			// Check if the value is an array, and convert it to a string if necessary
			if (is_array($value)) {
				$value = implode(', ', $value); // Join array elements into a string
			}

			$rows[] = [$key, $value];
		}

		$table->setRows($rows);
		$table->render();

		return Command::SUCCESS;
	}
}
