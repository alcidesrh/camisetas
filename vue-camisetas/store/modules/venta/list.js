import fetch from '../../../utils/fetch';
import {
  VENTA_LIST_ERROR,
  VENTA_LIST_LOADING,
  VENTA_LIST_RESET,
  VENTA_LIST_VIEW,
  VENTA_LIST_SUCCESS
} from './mutation-types';

const state = {
  loading: false,
  error: '',
  items: [],
  view: []
};

function error(error) {
  return {type: VENTA_LIST_ERROR, error};
}

function loading(loading) {
  return {type: VENTA_LIST_LOADING, loading};
}

function success(items) {
  return {type: VENTA_LIST_SUCCESS, items};
}

function view(items) {
  return { type: VENTA_LIST_VIEW, items};
}

const getters = {
  error: state => state.error,
  items: state => state.items,
  loading: state => state.loading,
  view: state => state.view
};

const actions = {
    getItems({ commit }, page = '/ventas') {
      commit(loading(true));

        return fetch(page)
        .then(response => response.json())
        .then(data => {
          commit(loading(false));
          commit(success(data['hydra:member']));
          commit(view(data['hydra:view']));
        })
        .catch(e => {
          commit(loading(false));
          commit(error(e.message));
        });
    }
};

const mutations = {
    [VENTA_LIST_ERROR] (state, payload) {
      state.error = payload.error;
    },
    [VENTA_LIST_LOADING] (state, payload) {
      state.loading = payload.loading;
    },
    [VENTA_LIST_VIEW] (state, payload) {
      state.view = payload.items;
    },
    [VENTA_LIST_SUCCESS] (state, payload) {
      state.items = payload.items;
    },
    [VENTA_LIST_RESET] (state) {
      state.items = [];
    }
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}
