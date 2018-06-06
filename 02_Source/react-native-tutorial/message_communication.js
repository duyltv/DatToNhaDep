import axios from 'axios';

import { SERVER } from './configs'
import { Base64 } from './Base64'

export async function Message(mcode, json_data) {
  var utf8 = require('utf8');
  var binaryToBase64 = require('binaryToBase64');

  var input_data="";
  var is_first=true;
  for (var key in json_data) {
    if (is_first)
    {
      input_data=input_data+"\""+key+"\":\""+json_data[key]+"\"";
      is_first=false;
    }
    else
    {
      input_data=input_data+", \""+key+"\":\""+json_data[key]+"\"";
    }
  }

  var text = '{"mcode":"'+mcode+'", '+input_data+'}';
  var encoded = Base64.btoa(text);
  var result="";

  const querystring = require('querystring');

  await axios.post(SERVER, querystring.stringify({ data: encoded }))
  .then(res => {
    console.log(res.data);
    result = res.data;
  }).catch((error) => {
      console.error(error);
  });

  return result.output;
};
