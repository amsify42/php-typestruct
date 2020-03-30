<?php

namespace Amsify42\Tests\TypeStruct;

export typestruct Simple {
	id: int<checkIdPrice>,
	type: string<checkEnumTypes>,
	details: {
		number: int,
		price: float
	}
}