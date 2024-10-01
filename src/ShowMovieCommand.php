<?php

namespace App;

use GuzzleHttp\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShowMovieCommand extends Command {
  protected static $defaultName = 'show';

  protected function configure() {
    $this->setDescription('Fetches information about a movie from OMDB.')
         ->addArgument('title', InputArgument::REQUIRED, 'The title of the movie.');
  }

  protected function execute(InputInterface $input, OutputInterface $output): int {
    $title = $input->getArgument('title');

    // Create a Guzzle client
    $client = new Client();

    // Access the API key from the environment
    $apiKey = $_ENV['OMDB_API_KEY'];

    // Make the API request
    $response = $client->request('GET', 'http://www.omdbapi.com/', [
      'query' => [
        't' => $title,
        'apiKey' => $apiKey
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
    $table->setRows([
            ['Title', $movieData['Title']],
            ['Year', $movieData['Year']],
            ['Rated', $movieData['Rated']],
            ['Released', $movieData['Released']],
            ['Runtime', $movieData['Runtime']],
            ['Genre', $movieData['Genre']],
            ['Director', $movieData['Director']],
            ['Writer', $movieData['Writer']],
            ['Actors', $movieData['Actors']],
            ['Plot', $movieData['Plot']],
            ['Language', $movieData['Language']],
            ['Country', $movieData['Country']],
            ['Awards', $movieData['Awards']],
            ['Poster', $movieData['Poster']],
            ['Metascore', $movieData['Metascore']],
            ['imdbRating', $movieData['imdbRating']],
            ['imdbVotes', $movieData['imdbVotes']],
            ['imdbID', $movieData['imdbID']],
            ['Type', $movieData['Type']],
            ['DVD', $movieData['DVD']],
            ['BoxOffice', $movieData['BoxOffice']],
            ['Production', $movieData['Production']],
            ['Website', $movieData['Website']],
            ['Response', $movieData['Response']],
          ]);

    $table->render();

    return Command::SUCCESS;
  }
}