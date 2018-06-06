'use strict';

import React, { Component } from 'react'
import Geocoder from 'react-native-geocoding';
import {
  StyleSheet,
  View
} from 'react-native';

import MapView, { Marker } from 'react-native-maps';

const styles = StyleSheet.create({
  container: {
    ...StyleSheet.absoluteFillObject,
    height: '100%',
    width: 400,
    justifyContent: 'flex-end',
    alignItems: 'center',
  },
  map: {
    ...StyleSheet.absoluteFillObject,
  },
});

class MapsPage extends React.Component {
  render() {
    var region={
            latitude: this.props.navigation.state.params.latitude,
            longitude: this.props.navigation.state.params.longitude,
            latitudeDelta: 0.015,
            longitudeDelta: 0.0121,
          };
    console.log(region);
    return (
      <View style ={styles.container}>
        <MapView
          style={styles.map}
          region={region}
          height="100%"
        >
            <Marker
              coordinate={{
                latitude: region.latitude,
                longitude: region.longitude,
              }}
              title={this.props.navigation.state.params.prop_name}
              description={this.props.navigation.state.params.prop_price}
            />
        </MapView>
      </View>
    );
  }
}

module.exports = MapsPage;
