# phpStringCompare

Original code is from [hakanbolat/phpStringCompare](https://github.com/hakanbolat/phpStringCompare)

Compares two strings and outputs the similarities  as percentage with PHP

##Usage:

```php
    require_once 'StringPercentCompare.php';
    $compareStr = 'Asus ROG GL553VD-DM066 i7-7700HQ 2.80GHz 8GB 128GB SSD+1TB 15.6" FHD 4GB GTX 1050 FreeDOS Gaming Notebook';
    $comparedStr = 'ASUS GL553VD-DM065T i7-7700HQ/ 8 GB DDR4/1TB 5400RPM-128G M.2 SSD/4 GB NVIDIA GeForce GTX 1050/W10/GAMING NOTEBOOK';
  
    $compare = new StringPercentCompare($compareStr, $comparedStr);
  
    echo $compare->getSimilarityPercentage();
```

##Replace words

```php
    $compare = new StringPercentCompare($compareStr, $comparedStr);
    
    $compare->replaceList = array(
        'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g',
        'д' => 'd', 'е' => 'e', 'ж' => 'j', 'з' => 'z',
        'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l',
        'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p',
        'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u',
        'ф' => 'f', 'х' => 'h', 'ц' => 'ts', 'ч' => 'ch',
        'ш' => 'sh', 'щ' => 'sch', 'ъ' => 'y', 'ы' => 'yi',
        'ь' => "'", 'э' => 'e', 'ю' => 'yu', 'я' => 'ya'
    );
    
    echo $compare->getSimilarityPercentage();
```

##Remove words

```php
    $compare = new StringPercentCompare($compareStr, $comparedStr);
    
    $compare->removeList = array(
        'success', 'item'
    );
    
    echo $compare->getSimilarityPercentage();
```
