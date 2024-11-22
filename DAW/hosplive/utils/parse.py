def parseAndWriteCitiesData():
    #Reading the cities data
    cities_in = open('resources/cities.txt', 'r')
    cities_counties = cities_in.readlines()

    #Opening the output files for parsed data
    cities_out = open('resources/cities1.txt', 'w')
    counties_out = open('resources/counties.txt', 'w')

    #Prepaing the headers
    cities_out.write("city_id city_name county_id\n")
    counties_out.write("county_id county_name\n")

    # county_name -> county_id
    set_counties = {}

    city_id = 1
    county_id = 1
    for city_county in cities_counties:
        # Removing the new line character
        city_county = city_county.replace('\n', '')

        # Splitting the city and county
        split_city_county = city_county.split(' ')

        # Adding the county to the set of counties
        if split_city_county[1] not in set_counties:
            set_counties[split_city_county[1]] = county_id
            county_id += 1
        
        new_line = '\n'
        # Last city doesn't have a new line
        if city_id == len(cities_counties):
            new_line = ''

        # Writing the cities data
        cities_out.write(str(city_id) + ', ' + split_city_county[0] + ', ' 
                         + str(set_counties[split_city_county[1]]) + new_line)

        city_id += 1

    printCountiesData(counties_out, set_counties)
    
    cities_in.close()
    cities_out.close()
    counties_out.close()

def printCountiesData(counties_out, set_counties):
    i = 0
    # Writing the counties data
    for county_name, county_id in set_counties.items():
        new_line = '\n'
        # Writing the last county without a new line
        if i == len(set_counties) - 1:
            new_line = ''
        # Writing the counties data
        counties_out.write(str(county_id) + ', ' + county_name + new_line)
        i += 1

def parseAndWriteMedicsData():
    file_in = open('resources/medics.txt', 'r', encoding='utf-8')
    lines = file_in.readlines()

    file_out = open('resources/medics.txt', 'w', encoding='utf-8')

    for line in lines:
        file_out.write(parseMedicData(line))
    
    file_in.close()
    file_out.close()

def parseMedicData(data : str) -> str:
    diacritics = ['ă', 'î', 'ș', 'ț', 'â', 'Ă', 'Î', 'Ș', 'Ț', 'Â']
    antidiacritics = ['a', 'i', 's', 't', 'a', 'A', 'I', 'S', 'T', 'A']
    address_forms = ['ing.', 'd-soara', 'dl.', 'dr.', 'd-na.']

    #Replacing diacritics with antidiacritics
    for diacritic, antidiacritic in zip(diacritics, antidiacritics):
        data = data.replace(diacritic, antidiacritic)
    
    data = data.split(' ')
    #Ignoring address forms
    if data[1] in address_forms:
        return data[0] + ' ' + ' '.join(data[2:])
    return ' '.join(data)

parseAndWriteCitiesData()
#parseAndWriteMedicsData()