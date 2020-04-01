<?php

namespace Amsify42\Tests\TypeStruct;

use Amsify42\Tests\TypeStruct\User;

export typestruct Complicated {
    name: string,
    user : User,
    address: {
        door: string,
        zip: int
    },
    url: string<url>,
    items: [],
    someEl: {
        key1: string,
        key2: int,
        key12: array,
        records: Amsify42\Tests\TypeStruct\Record[],
        someChild: {
            key3: boolean,
            key4: float,
            someAgainChild: {
                key5: string,
                key6: float,
                key56: boolean[]
            }
        }
    }
}