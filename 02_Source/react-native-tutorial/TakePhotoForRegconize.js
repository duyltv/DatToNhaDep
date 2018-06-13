'use strict';
import React, { Component } from 'react'
import {
  StyleSheet,
  Image,
  View,
  ScrollView,
  TouchableHighlight,
  Text
} from 'react-native';
var ImagePicker = require('react-native-image-picker');

var options = {
  title: 'Chọn hình ảnh',
  storageOptions: {
    skipBackup: true,
    path: 'images'
  }
};

class TakePhotoForRegconize extends Component {
	static navigationOptions = {
		title: 'Chọn hình ảnh'
	};

	constructor(props) {
	    super(props);
	    this.state = {
	      taken_image: ""
	    };

	    ImagePicker.showImagePicker(options, (response) => {
		  console.log('Response = ', response);

		  if (response.didCancel) {
		    console.log('User cancelled image picker');
		  }
		  else if (response.error) {
		    console.log('ImagePicker Error: ', response.error);
		  }
		  else if (response.customButton) {
		    console.log('User tapped custom button: ', response.customButton);
		  }
		  else {
		    let source = { uri: response.uri };

		    // You can also display the image using data:
		    // let source = { uri: 'data:image/jpeg;base64,' + response.data };

		    this.setState({
		      taken_image: source
		    });
		  }
		});
	  }

	render() {

    console.log('TakePhotoForRegconize.render');
    return (
    	<View>
    	{ this.state.taken_image === "" ? <Text>Select a Photo</Text> :
		    <Image style={{width: 130, height: 140}} source={this.state.taken_image} />
		}
    	</View>
    	)
    }
}


module.exports = TakePhotoForRegconize;