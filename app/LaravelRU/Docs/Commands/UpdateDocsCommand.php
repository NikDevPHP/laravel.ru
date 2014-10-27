<?php namespace LaravelRU\Docs\Commands;

use Carbon\Carbon;
use Github\Client;
use Indatus\Dispatcher\Scheduling\ScheduledCommand;
use Indatus\Dispatcher\Scheduling\Schedulable;
use Indatus\Dispatcher\Drivers\Cron\Scheduler;
use Docs;
use LaravelRU\Github\GithubRepo;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class UpdateDocsCommand extends ScheduledCommand {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'su:update_docs';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Update russian docs from translated github repo.';

	/**
	 * @var \LaravelRU\Github\GithubRepo
	 */
	private $githubTranslated;
	private $githubOriginal;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
		$this->githubTranslated =   new GithubRepo(\Config::get("laravel.translated_docs.user"), \Config::get("laravel.translated_docs.repository"));
		$this->githubOriginal =     new GithubRepo(\Config::get("laravel.original_docs.user"),   \Config::get("laravel.original_docs.repository"));
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$github = new Client();
		$http = new \Guzzle\Http\Client();

		$forceupdate = $this->argument("force");
		foreach(\Config::get("laravel.versions") as $version){

			$this->info("Process branch $version");

			if($forceupdate){
				Docs::version($version)->delete();
				$this->info("force clear database!");
			}

			//$content = $this->githubTranslated->getFile($version, "menu.md");
			$this->line("Fetch documentation.md");
			$content = $this->githubTranslated->getFile($version, "documentation.md");
			$lines = explode("\n", $content);
			$lines[] = "[Menu](/docs/$version/documentation)";
			$matches = array();
			foreach($lines as $line){
				preg_match("/\(\/docs\/(.*?)\/(.*?)\)/im", $line, $matches);
				if(isset($matches[2])){
					$name = $matches[2];
					$filename = $name.".md";
					$this->line("");
					$this->line("Fetch $filename ..");
					//$last_commit_id = $this->githubTranslated->getLastCommitId($version, $filename);
					$this->line(" get last translated commit");
					$commit = $this->githubTranslated->getLastCommit($version, $filename);

					if( ! is_null($commit)) {
						$last_commit_id = $commit['sha'];
						$last_commit_at = Carbon::createFromTimestampUTC(strtotime($commit['commit']['committer']['date']));
						$this->line(" get file");
						$content = $this->githubTranslated->getFile($version, $filename, $last_commit_id);
						if(!is_null($content)) {
							preg_match("/git (.*?)$/m", $content, $matches);
							$last_original_commit_id = array_get($matches, '1');
							//if(!$last_original_commit) {
							if(!$last_original_commit_id AND $name != "menu") {
								$this->error("Not found git signature in $filename");
							}
							else {
								$this->line(" get last translated original commit $last_original_commit_id");
								$original_commit = $this->githubOriginal->getCommit($last_original_commit_id);
								$count_ahead = 0;
								$current_original_commit = "";
								if($original_commit) {
									$last_original_commit_at = Carbon::createFromTimestampUTC(strtotime($original_commit['commit']['committer']['date']));

									// Считаем сколько коммитов прошло с момента перевода
									$this->line(" get current original commit");
									$original_commits = $this->githubOriginal->getCommits($version, $filename, $last_original_commit_at);
									$count_ahead = count($original_commits)-1;
									$current_original_commit = $this->githubOriginal->getLastCommit($version, $filename);
									$current_original_commit_id = $current_original_commit['sha'];

//									$current_original_commit = $original_commits[0]['sha'];
//									foreach($original_commits as $c){
//										if($c['sha']!=$last_original_commit_id) $count_ahead++;
//									}

								}
								else {
									$last_original_commit_at = null;
								}

								$content = preg_replace("/git(.*?)(\n*?)---(\n*?)/", "", $content);
								preg_match("/#(.*?)$/m", $content, $matches);
								$title = trim(array_get($matches, '1'));
								$page = Docs::version($version)->name($name)->first();
								if($page) {
									if($last_commit_id != $page->last_commit) {
										$page->last_commit = $last_commit_id;
										$page->last_commit_at = $last_commit_at;
										$page->last_original_commit = $last_original_commit_id;
										$page->last_original_commit_at = $last_original_commit_at;
										$page->current_original_commit = $current_original_commit_id;
										$page->original_commits_ahead = $count_ahead;
										$page->title = $title;
										$page->text = $content;
										$page->save();
										$this->info("$version/$filename updated. Commit $last_commit_id. Last original commit $last_original_commit_id.");
									}
								}
								else {
									Docs::create([
											'framework_version' => $version,
											'name' => $name,
											'title' => $title,
											'last_commit' => $last_commit_id,
											'last_commit_at' => $last_commit_at,
											'last_original_commit' => $last_original_commit_id,
											'last_original_commit_at' => $last_original_commit_at,
											'current_original_commit' => $current_original_commit_id,
											'original_commits_ahead' => $count_ahead,
											'text' => $content]);
									$this->info("$version/$filename created. Commit $last_commit_id. Last original commit $last_original_commit_id.");
								}

							}
						}
					}

				}
			}

		}
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
				array('force', InputArgument::OPTIONAL, 'Delete all docs and replace by github data.'),
		);
	}

	/**
	 * When a command should run
	 *
	 * @param Scheduler $scheduler
	 * @return \Indatus\Dispatcher\Scheduling\Schedulable
	 */
	public function schedule(Schedulable $scheduler)
	{
		return $scheduler->everyMinutes(20);
	}

}