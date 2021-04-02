import { mount } from "@vue/test-utils"
import TableFilter from "../js/Tailwind2/TableFilter.vue";
import expect from 'expect'

describe('TableFilter.vue', () => {
    it('renders a select for each filter', () => {
        let changes = [];

        const options = {
            '': '-',
            'a': 'Option A',
            'b': 'Option B'
        };

        const component = mount(TableFilter, {
            propsData: {
                filters: {
                    name: {
                        key: "name",
                        label: "Name",
                        value: null,
                        options
                    },

                    email: {
                        key: "email",
                        label: "Email",
                        value: null,
                        options
                    }
                },

                onChange(key, value) {
                    changes.push({ key, value })
                }
            }
        });

        expect(component.vm.hasEnabledFilter).toBeFalsy()

        const selects = component.findAll("select");
        expect(selects).toHaveLength(2);

        selects.at(0).setValue("a");
        selects.at(1).setValue("b");

        expect(changes).toHaveLength(2);

        expect(changes[0]).toEqual({
            key: "name", value: "a"
        })

        expect(changes[1]).toEqual({
            key: "email", value: "b"
        })
    });


    it('knows wether a filter has been enabled', () => {
        const options = {
            '': '-',
            'a': 'Option A',
            'b': 'Option B'
        };

        const component = mount(TableFilter, {
            propsData: {
                filters: {
                    name: {
                        key: "name",
                        label: "Name",
                        value: "a",
                        options
                    },

                    email: {
                        key: "email",
                        label: "Email",
                        value: null,
                        options
                    }
                },

                onChange() { }
            }
        });

        expect(component.vm.hasEnabledFilter).toBeTruthy()
    });
})