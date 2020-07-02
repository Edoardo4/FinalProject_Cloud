
import simplejson as json
import requests
from flask import request, url_for, Flask, Response
import PIL.ExifTags
import PIL.Image
from DbManager import dbManager
from Helper import Helper
from datetime import datetime
import time

CV_TOKEN = 'fc104e1031304ebb933c4404c853ba16'   #Insert here token azure cognitive service

CV_URL = 'https://westeurope.api.cognitive.microsoft.com/vision/v2.0/analyze'   # change endpoint
CV_PARAMS = {'visualFeatures': "Tags,Categories,Faces,Adult"}
CV_HEADER = {'Content-Type': 'application/json', 'Ocp-Apim-Subscription-Key': CV_TOKEN}

app = Flask(__name__)

@app.route('/ComputerVision/<username>', methods=['POST', 'GET']) 
def ComputerVision(username):

    if request.method == 'POST':    #body with image_url, image_name, image_tmp

        if not request.is_json:
            return Response("{'Error':'Body not a json', 'Error_code':'40'}", status=400, mimetype='application/json')

        data = request.get_json()

        result = dbManager.getJsonImage(username, data['image_name'])

        if result:
            return Response("{'Warning':'Json already in the database', 'Error_code':'20'}", status=200, mimetype='application/json')

        rsp = requests.request('POST', CV_URL, params=CV_PARAMS, json={'url' : data['image_url']}, headers=CV_HEADER)

        img = PIL.Image.open(data['image_tmp'])
        exif = Helper.get_exif_data(img)
        # exif = img._getexif()
        # exif_data = None
        # if not (exif == None):
        #     exif_data = {
        #         PIL.ExifTags.TAGS[k]: v
        #         for k, v in img._getexif().items()
        #         if k in PIL.ExifTags.TAGS
        #     }

        dbManager.insertJsonImage(username, data['image_name'], json.dumps(rsp.json()), json.dumps(exif, encoding='latin1'))

        return Response(status=200)

    if request.method == 'GET':     #parameters with categories or/and tags
        tags = None
        if 'tags' in request.args:
            tags = request.args.get('tags').split(',')
        results = dbManager.getFilteredJsonImage(username, tags)
        return Response(json.dumps(results), status=200, mimetype='application/json')

@app.route('/ComputerVision/<username>/positions') 
def GetPositionImages(username):
    res = dbManager.getPositions(username)
    return Response(json.dumps(res), status=200, mimetype='application/json')

@app.route('/Shared/<username>/<imageName>') 
def GetExpiredTime(username, imageName):
    res = dbManager.getExpirationTime(username, imageName)
    expired = None;
    if res > datetime.now():
        expired = False
    if res == None:
        expired = True
    expired = {'expired' : expired}
    return Response(json.dumps(expired), status=200, mimetype='application/json')

@app.route('/Shared/<username>/<imageName>', methods=['PATCH']) 
def SetExpiredTime(username, imageName):
    if not request.is_json:
        return Response("{'Error':'Body not a json', 'Error_code':'40'}", status=400, mimetype='application/json')

    data = request.get_json()
    expirationTime = data['expirationDate']
    format = '%d-%m-%Y %X'
    expirationTime = datetime.strptime(expirationTime, format)
    res = dbManager.setExpirationTime(username, imageName, expirationTime)
    return Response(status=200)

@app.route('/ComputerVision/<username>/<imageName>', methods=['GET'])   #parameter confidence float number between 0 and 1
def GetImageDetails(username, imageName):
    confidence = request.args.get('confidence')
    confidence = 0.8 if confidence == None else float(confidence)
    data = dbManager.getJsonImage(username, imageName)
    if data == None:
        return Response("{'Error':'Image name not found for this user', 'Error_code':'44'}", status=404, mimetype='application/json')
    dataJson = json.loads(data[0])
    dataExif = json.loads(data[1])

    tags = None
    categories = None

    if 'tags' in dataJson:
        tags = list(filter(lambda x: float(x['confidence']) > confidence, dataJson['tags']))
        tags = list(map(lambda x: x['name'], tags))
    if 'categories' in dataJson:
        categories = list(filter(lambda x: float(x['score']) > confidence, dataJson['categories']))
        categories = list(map(lambda x: x['name'], categories))
    exifRes = {}
    if dataExif:
        exifRes['dateTime'] = dataExif['DateTimeOriginal'] if 'DateTimeOriginal' in dataExif else ''
        exifRes['position'] = Helper.get_lat_lon(dataExif)
        exifRes['make'] = dataExif['Make'] if 'Make' in dataExif else ''
        exifRes['model'] = dataExif['Model'] if 'Model' in dataExif else ''
        
    result = {
        'tags' : tags,
        'categories' : categories,
        'faces' : dataJson['faces'] if 'faces' in dataJson else [],
        'dateTime' : exifRes['dateTime'] if 'dateTime' in exifRes else '',
        'position' : json.dumps(exifRes['position']) if 'position' in exifRes else '',
        'make' : exifRes['make'] if 'make' in exifRes else '',
        'model' : exifRes['model'] if 'model' in exifRes else '',
        }
    return Response(json.dumps(result), status=200, mimetype='application/json')

@app.route('/ComputerVision/<username>/<imageName>', methods=['DELETE']) 
def DeleteImageFromDb(username, imageName):
    res = dbManager.deleteImage(username, imageName)
    if res != 1:
        return Response("{'Error':'Error on delete data from db', 'Error_code':'41'}", status=400, mimetype='application/json')
    return Response(status=200)

if __name__ == "__main__":
    app.run(host='0.0.0.0')
