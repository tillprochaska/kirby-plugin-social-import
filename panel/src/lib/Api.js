const BASE_URL = '/plugin/tillprochaska/social-import';

export default class { 

    static async getImportablePreview(url) {
        return this.get('importables/previews', { url });
    }

    static async getImportableForm(url) {
        return this.get('importables/forms', { url });
    }

    static async createImportablePage(url, data) {
        return this.post('importables/pages', { url }, data);
    }

    static async get(route, params = {}) {
        let url = this.buildUrl(route, params);

        let response;
        response = await fetch(url);
        response = await response.json();

        if(response.status !== 'ok') {
            throw new Error(response.message);
        }

        return response.data;
    }

    static async post(route, params = {}, data = {}) {
        let url = this.buildUrl(route, params);

        let response;
        let formData = new FormData();

        for(let key in data) {
            formData.append(key, data[key]);
        }

        response = await fetch(url, {
            method: 'POST',
            body: formData,
        });
        response = await response.json();

        if(response.status !== 'ok') {
            throw new Error(response.message);
        }

        return response.data;
    }

    static buildUrl(route, params = {}) {
        let queryString = Object.entries(params).map(([key, value]) => {
            return `${ key }=${ encodeURIComponent(value) }`;
        });

        return `${ BASE_URL }/${ route }?${ queryString }`;
    }

};
