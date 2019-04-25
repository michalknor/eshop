eshop - modely drakov

inštalácia:
	- priečinok "eshop" presunúť na lokálny server (na živý som netestoval)
	- importovať databázu "eshop.sql" a upraviť meno a heslo v súbore "config/database.php"

api neobsahuje:
	- unit testy
	- design

popis: 
	- tabuľky orders_products_not_finished a orders_not_finished prislúchajú k nedokončenej objednávke a user_id je unique
	- každý používateľ má vygenerovaný token, pomocou ktorého sa vie dostať k exportu svojich objednávok (json), a svojho účtu (json)
	- každý sa vie dostať k api produktov (meniť databázu zvonka nevie)
	- užívateľ vytvorí objednávku / pridá do objednávky po kliknutí na order pri detaile produktu
	- užívateľ dokončí svoju objednávku po kliknutí na tlačidlo finish (z orders_products_not_finished a orders_not_finished sa presunú a následne vymažú informácie o objednávke)
