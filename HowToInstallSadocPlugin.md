# Introduction #

We will install SADOC plugin for moodle

# Details #

Step 1 : Follow this wiki before starting [HowToInstallMoodle](http://code.google.com/p/sadoc-plugin/wiki/HowToInstallMoodle)

Step 2 : Now please follow this other wiki before installing the plugin [HowToInstallReferentialPluginForMoodle](http://code.google.com/p/sadoc-plugin/wiki/HowToInstallReferentialPluginForMoodle)

Step 3 : Download sources [here](http://code.google.com/p/sadoc-plugin/downloads/detail?name=source_plugin_sadoc.zip&can=2&q=#makechanges)

Step 4 : Uncompress the sources in the /var/www/moodle/mod/referentiel/ directory
```
# cd /var/www/moodle/mod/referentiel/
# sudo tar -xzvf ~/Downloads/source_plugin_sadoc.tar.gz
# sudo chown root:root -R SADOC/ (optional)
# sudo chmod 755 SADOC/
# cd SADOC/
# sudo chmod 747 PDF/
# sudo chmod 755 IMG/
```