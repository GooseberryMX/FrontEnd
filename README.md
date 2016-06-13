
Please, run ```php composer.phar config --global github-oauth.github.com 0e369c1ad118e117c74a48887736cf80df69f545```
before running composer install, otherwise auth-bundle does not install and you'll get error like this
 >Your GitHub credentials are required to fetch private repository metadata (https://github.com/crowdvalley/authbundle)
 >Head to https://github.com/settings/tokens/new?scopes=repo&description=Composer+on+block+2015-07-06+1151
 >to retrieve a token. It will be stored in "/home/tero/.composer/auth.json" for future use by Composer.
 >Token (hidden): 


