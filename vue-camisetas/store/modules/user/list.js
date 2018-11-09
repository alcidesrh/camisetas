import fetch from '../../../utils/fetch';
import {
  USER_LIST_ERROR,
  USER_LIST_LOADING,
  USER_LIST_RESET,
  USER_LIST_VIEW,
  USER_LIST_SUCCESS
} from './mutation-types';

const state = {
  loading: false,
  error: '',
  items: [],
  view: []
};

function error(error) {
  return {type: USER_LIST_ERROR, error};
}

function loading(loading) {
  return {type: USER_LIST_LOADING, loading};
}

function success(items) {
  return {type: USER_LIST_SUCCESS, items};
}

function view(items) {
  return { type: USER_LIST_VIEW, items};
}

const getters = {
  error: state => state.error,
  items: state => state.items,
  loading: state => state.loading,
  view: state => state.view
};

const actions = {
    getItems({ commit }, page = '/users') {
      commit(loading(true));

      fetch(page)
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
    [USER_LIST_ERROR] (state, payload) {
      state.error = payload.error;
    },
    [USER_LIST_LOADING] (state, payload) {
      state.loading = payload.loading;
    },
    [USER_LIST_VIEW] (state, payload) {
      state.view = payload.items;
    },
    [USER_LIST_SUCCESS] (state, payload) {
      state.items = payload.items;
    },
    [USER_LIST_RESET] (state) {
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
