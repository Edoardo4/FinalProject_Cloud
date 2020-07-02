import mysql.connector
from Helper import Helper
import simplejson as json
from datetime import datetime

class dbManager:
    
    config = {
        'user': '',     #Insert here db user and password 
        'password': '',
        'host': '127.0.0.1',
        'database': 'finalproject',
        'raise_on_warnings': True
    }

    @classmethod
    def connect(self):
        try:
            return mysql.connector.connect(**self.config)

        except Exception:
            return None

    @classmethod
    def getJsonImage(self, username, image_name):

        cnx = self.connect()

        cursor = cnx.cursor()
        QUERY = "SELECT image_json, image_exif FROM images WHERE username = %s AND image_name = %s"
        cursor.execute(QUERY, (username, image_name))
        data_json = cursor.fetchone()
        cursor.close()
        cnx.close()

        return data_json

    @classmethod
    def insertJsonImage(self, username, image_name, image_json, image_exif = None):
        
        cnx = self.connect()

        cursor = cnx.cursor()
        QUERY = "INSERT INTO images VALUES (%s, %s, %s, %s, %s, %s)"

        cursor.execute(QUERY, (username, image_name, image_json, image_exif, None, datetime.now()))

        cnx.commit()
        cursor.close()
        cnx.close()

        return 1

    @classmethod
    def getFilteredJsonImage(self, username, tags = None):
        TAGS_QUERY = ' JSON_CONTAINS(image_json, \'{{"name" : "{}"}}\', "$.tags")'

        cnx = self.connect()

        cursor = cnx.cursor()
        QUERY = "select image_name from images where username = '{}'".format(username)
        firstContainsFlag = False
        
        if tags:
            QUERY += ' and ('
            for tag in tags:
                if not firstContainsFlag:
                    QUERY += TAGS_QUERY.format(tag)
                    firstContainsFlag = True
                    continue
                QUERY += ' or' + TAGS_QUERY.format(tag)
            QUERY += ')'
        
        QUERY += ' order by upload_time desc'

        cursor.execute(QUERY)
        data_json = cursor.fetchall()
        cursor.close()
        cnx.close()

        res = list()
        
        for data in data_json:
            res.append(data[0])
            
        return res

    @classmethod
    def getPositions(self, username):
        cnx = self.connect()

        cursor = cnx.cursor()
        QUERY = "SELECT image_name, image_exif FROM images WHERE username = %s"
        cursor.execute(QUERY, (username,))
        data_json = cursor.fetchall()
        res = {}
        for x in data_json:
            lonLat = Helper.get_lat_lon(json.loads(x[1]))
            if lonLat == None:
                continue
            res[x[0]] = lonLat

        cursor.close()
        cnx.close()

        return res

    @classmethod        #TODO
    def deleteImage(self, username, imageName):
        
        cnx = self.connect()

        cursor = cnx.cursor()
        QUERY = "DELETE FROM images WHERE username = %s AND image_name = %s"

        cursor.execute(QUERY, (username, imageName))

        cnx.commit()
        cursor.close()
        cnx.close()

        return 1

    @classmethod
    def getExpirationTime(self, username, imageName):
        cnx = self.connect()

        cursor = cnx.cursor()
        QUERY = "SELECT expiration_time FROM images WHERE username = %s AND image_name = %s"
        cursor.execute(QUERY, (username, imageName))
        data_json = cursor.fetchone()
        cursor.close()
        cnx.close()

        return data_json[0]

    @classmethod
    def setExpirationTime(self, username, imageName, expirationTime):
        cnx = self.connect()

        cursor = cnx.cursor()
        QUERY = "UPDATE images SET expiration_time = %s WHERE username = %s AND image_name = %s"
        cursor.execute(QUERY, (expirationTime, username, imageName))
        cnx.commit()
        cursor.close()
        cnx.close()

        return 1

