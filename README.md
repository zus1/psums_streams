<h3>About</h3>
Psums project is created to analyze lorem ipsum stream by applying pre defined rules<br>
It is composed of 3 services<br><br>
<ol>
    <li><a href="https://github.com/zus1/psums_aggregator">Aggregator</a></li>
    <li><a href="https://github.com/zus1/psums_streams">Streams</a></li>
    <li><a href="https://github.com/zus1/psums-api">Api</a></li>
</ol>

<h3>Installation for Psums project</h3>
Psums can be installed in two different ways

First is to pull all three services and put them in same directory (each service in its own directory
and then all put in same parent directory). Then go to Aggregator directory and run
<pre><code>docker-compose up</code></pre>
This will build up all containers, and those are following:
<ul>
    <li>psums_aggregator</li>
    <li>psums_streams</li>
    <li>psums_api</li>
    <li>psums_mysql</li>
    <li>psums_memcached</li>
</ul>
After build process i completed (may take a few minutes, depending if you have some images built already)
it necessary to bash into aggregator container and run migrations. Psums uses Phinx as migration engine.
<br><br>
<pre><code>docker container exec -it bash psums_aggregator</code></pre>
Yous should be in /var/www/html direcotry, now run migrations
<br><br>
<pre><code>php library/phinx/bin/phinx migrate</code></pre>
Now you should be all set up
<br><br>

Second way is by using <a href="https://github.com/zus1/psums_compose">Psums composer</a>, you can follow instalation instructions on that repository. 
It will install production version without access to code base

<h3>How to use Streams service</h3>
And another one that literally uses itself. Its completely and runs form shell, triggered by cron job. Cron will be 
automatically added when composing project. What Streams service dose is call third party api's and collect some nice lorem ipsum
goodies, then stores those word streams in database and at the same time sends n words to aggregator service for analysis.
Size of sent stream can be adjusted in <b>settings</b> table.<br><br>
Api's it currently uses are following:
<br><br>
<ul>
    <li><a href="http://asdfast.beobit.net/docs/">ADSFast</a></li>
    <li><a href="https://baconipsum.com/json-api/">BaconIpsum</a></li>
    <li><a href="https://hipsum.co/the-api/">Hipsum</a></li>
    <li><a href="http://metaphorpsum.com/">Metaphorpsum</a></li>
</ul>
By default setting it will run one refresh cycle (includes ispum refresh and calls to aggregator) each 4 minutes.
And its sending 20 words to aggregator each cycle.

<h3>How i know whats going on?</h3>
Check table <b>streams_api_call_log</b> to check api responses from ipsum apis and also from aggregator.
This will tell you status of each api. You can also tail cron.log for report of each refresh cycle. Bash into psums_stream container,
 go to /var/log directory and tun:
<br><br>
<pre><code>tailf -f cron.log</code></pre>
This will tail corn.log and display each entry to command line.
<br><br>
That's it from Streams service. Check other services repositories to pick up some know how. And have fun :) 