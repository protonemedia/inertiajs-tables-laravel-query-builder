import { mount } from "@vue/test-utils"
import TableGlobalSearch from "../js/Tailwind2/TableGlobalSearch.vue";
import expect from 'expect'

describe('TableGlobalSearch.vue', () => {
    it('triggers a change when gets typed into the input', () => {
        let changes = [];
        const component = mount(TableGlobalSearch, {
            propsData: {
                value: "",

                onChange(value) {
                    changes.push(value);
                }
            }
        });

        const input = component.find("input");
        input.setValue("@protone");

        expect(changes).toHaveLength(1);
        expect(changes[0]).toBe("@protone");
    });
})