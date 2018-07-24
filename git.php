<?php 

namespace Rasteiner\KirbyGit;

require_once(__DIR__ . '/vendor/autoload.php');

use Exception;
use Kirby;
use Kirby\Http\Request;

use Phpeople\Git\Branch;
use Phpeople\Git\Commit;
use Phpeople\Git\GitLogParser;

class Git {

  public static function check_git() {
    if(`git --version`) {
      return true;
    } else {
      return false;
    }
  }

  public static function check_repo() {
    return file_exists(kirby()->roots()->content() . '/.git');
  }

  public static function git_init() {
    chdir(kirby()->roots()->content());
    return `git init && git config user.name "rasteiner-git" && git config user.email "master@universe"`;
  }

  public static function get_started() {
    if(!static::auth()) throw new Exception("user ha no permission", 1);

    chdir(kirby()->roots()->content());
    if(!static::check_git()) throw new Exception('Git doesn\'t seem to be installed');
    if(!static::check_repo()) static::git_init();
  }

  public static function auth() {
    //is admin?
    $u = kirby()->user();
    return $u !== null && in_array($u->role()->name(), kirby()->option('rasteiner/kirbygit/roles', ['admin']));
  }


  /* route action closures here */


  public static function has_auth() {
    return function() {
      return ['result' => Git::auth()];
    };
  }

  public static function status() {
    return function() {
      Git::get_started();
      return ['result' => `git status`];
    };
  }

  public static function list_commits() {
    return function() {
      Git::get_started();

      $branch = new Branch('master');
      $parser = new GitLogParser();
      $commits = [];
      foreach ($parser->getCommits($branch) as $hash => $c) {
        $time = $c->getCommitterTime()->format('Y-m-d H:i:s');

        $commits[] = [
          'id' => $hash,
          'label' => $c->getSubject(),
          'description' => "$time ($hash)"
        ];
      }

      return ['result' => $commits];
    };
  }

  public static function create_commit() {
    return function() {
      Git::get_started();
      $message = kirby()->request()->data()['message'];
      if($message) {
        $message = escapeshellarg($message);
        $result = `git add -A && git commit -m $message 2>&1` . '';
      } else {
        $result = "No commit message given";
      }

      return ['result' => $result];
    };
  }

  public static function show_commit() {
    return function($hash) {
      Git::get_started();
      $hash = escapeshellarg($hash);
      return ['result' => `git show $hash --stat`];
    };
  }

  public static function checkout_commit() {
    return function($hash) {
      Git::get_started();

      $hash = escapeshellarg($hash);
      return ['result' => `git clean -fd && git revert --no-commit $hash..HEAD`];
    };
  }
}
