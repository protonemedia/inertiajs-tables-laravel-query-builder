import { mount } from "@vue/test-utils"
import InteractsWithQueryBuilder from "../js/InteractsWithQueryBuilder.vue";
import expect from 'expect'

describe('InteractsWithQueryBuilder.vue', () => {
    it('it merges the search rows and filters into one object', () => {
        const queryBuilderProps = {
            sort: "name",
            search: {
                name: { key: "name", value: "pro" },
                country: { key: "country", value: null },
            },
            filters: {
                email: { key: "email", options: { '': '-', 'a': 'Option A' }, value: "a" },
                status: { key: "status", options: { '': '-', 'a': 'Option A' }, value: "" }
            },
            columns: {
                name: { key: "name", enabled: true },
                email: { key: "email", enabled: false }
            },
        };

        const component = mount(InteractsWithQueryBuilder, {
            propsData: {
                queryBuilderProps,
            },

            render() { }
        });

        expect(component.vm.queryBuilderData).toEqual({
            sort: "name",
            page: 1,
            filter: {
                name: "pro",
                email: "a",
            },
            columns: ["name"],
        });

        expect(component.vm.queryBuilderString).toEqual(
            encodeURI("page=1&sort=name&filter[email]=a&filter[name]=pro&columns[0]=name")
        );
    });

    it('it can update the sort column', () => {
        const queryBuilderProps = {
            sort: "name",
            page: 2,
        };

        const component = mount(InteractsWithQueryBuilder, {
            propsData: {
                queryBuilderProps,
            },

            render() { }
        });

        //

        component.vm.sortBy("email");

        // it resets the page to 1
        expect(component.vm.queryBuilderData).toEqual({
            sort: "email",
            page: 1,
            filter: {},
            columns: []
        });

        expect(component.vm.queryBuilderString).toEqual(
            encodeURI("page=1&sort=email")
        );

        //

        component.vm.sortBy("email");

        expect(component.vm.queryBuilderData).toEqual({
            sort: "-email",
            page: 1,
            filter: {},
            columns: []
        });

        expect(component.vm.queryBuilderString).toEqual(
            encodeURI("page=1&sort=-email")
        );
    });

    it('it provides helper method for table header cells', () => {
        const queryBuilderProps = {
            sort: "name",
            columns: {
                name: { key: "name", enabled: true },
                email: { key: "email", enabled: false },
                country: { key: "country", enabled: false },
            },
        };

        const component = mount(InteractsWithQueryBuilder, {
            propsData: {
                queryBuilderProps,
            },

            render() { }
        });

        expect(component.vm.sortableHeader('name').sortable).toBeTruthy();
        expect(component.vm.staticHeader('name').sortable).toBeFalsy();

        expect(component.vm.sortableHeader('name').sort).toEqual('asc');
        expect(component.vm.sortableHeader('email').sort).toBeFalsy();
        expect(component.vm.sortableHeader('country').sort).toBeFalsy();
    });

    it('it provides helper method for table header cells to switch to sort order', () => {
        const queryBuilderProps = {
            sort: "name",
            columns: {
                name: { key: "name", enabled: true },
            },
        };

        const component = mount(InteractsWithQueryBuilder, {
            propsData: {
                queryBuilderProps,
            },

            render() { }
        });

        component.vm.sortableHeader('name').onSort('name');
        expect(component.vm.sortableHeader('name').sort).toEqual('desc');
    });

    it('it toggles a column without changing the page', () => {
        const queryBuilderProps = {
            columns: {
                name: { key: "name", enabled: true },
                email: { key: "email", enabled: false },
                country: { key: "country", enabled: false },
            },
            page: 2,
        };

        const component = mount(InteractsWithQueryBuilder, {
            propsData: {
                queryBuilderProps,
            },

            render() { }
        });

        expect(component.vm.showColumn('name')).toBeTruthy();
        expect(component.vm.showColumn('email')).toBeFalsy();
        expect(component.vm.showColumn('country')).toBeFalsy();

        component.vm.setQueryBuilder({
            columns: {
                name: { key: "name", enabled: true },
                email: { key: "email", enabled: true },
                country: { key: "country", enabled: false },
            },
            page: 2,
        });

        expect(component.vm.queryBuilderData).toEqual({
            sort: "",
            page: 2,
            filter: {},
            columns: ["name", "email"],
        });

        expect(component.vm.queryBuilderString).toEqual(
            encodeURI("page=2&columns[0]=name&columns[1]=email")
        );
    });

    it('it updates a filter and change the page to 1', () => {
        const queryBuilderProps = {
            search: {
                name: { key: "name", value: null },
            },
            page: 2,
        };

        const component = mount(InteractsWithQueryBuilder, {
            propsData: {
                queryBuilderProps,
            },

            render() { }
        });

        component.vm.setQueryBuilder({
            search: {
                name: { key: "name", value: "pro" },
            },
            page: 2,
        });

        expect(component.vm.queryBuilderData).toEqual({
            sort: "",
            page: 1,
            filter: { name: "pro" },
            columns: [],
        });

        expect(component.vm.queryBuilderString).toEqual(
            encodeURI("page=1&filter[name]=pro")
        );
    });
})