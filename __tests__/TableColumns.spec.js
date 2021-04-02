import { mount } from "@vue/test-utils"
import TableColumns from "../js/Tailwind2/TableColumns.vue";
import expect from 'expect'

describe('TableColumns.vue', () => {
    it('lists all the given columns', () => {
        let changes = [];

        const component = mount(TableColumns, {
            propsData: {
                columns: {
                    name: {
                        key: "name",
                        label: "Name",
                        enabled: true
                    },

                    email: {
                        key: "email",
                        label: "Email",
                        enabled: false
                    }
                },

                onChange(key, value) {
                    changes.push({ key, value })
                }
            }
        });

        component.vm.toggle("name");
        component.vm.toggle("email");

        expect(changes).toHaveLength(2);

        expect(changes[0]).toEqual({
            key: "name", value: false
        })

        expect(changes[1]).toEqual({
            key: "email", value: true
        })
    });
})