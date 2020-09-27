<p align="center">
    <h1 align="center">DigitalHub ERP-Orders Test</h1>
    <br>
</p>

<h3>Instructions</h3>

<ul>
    <li>1. Go to the Magento root directory</li>
    <li>1. Run the command: <code>composer config repositories.kin-allan-dh-automation git https://github.com/kin-allan/dh-automation</code></li>
    <li>2. Then: <code>composer require kin-allan/dh-automation:1.0.0</code></li>
    <li>3. After the composer process is finished, run those commands:</li>
    <li><code>php bin/magento module:enable DigitalHub_Automation</code></li>
    <li><code>php bin/magento setup:upgrade</code></li>
    <li><code>php bin/magento setup:di:compile</code></li>
    <li><code>php bin/magento cache:flush</code></li>
</ul>
