<div data-index="{{ $index }}" class="card-bahan-pointer flex flex-col gap-[0.25rem] md:gap-[0.5rem] justify-center min-h-[4.5rem] max-h-[4.5rem] min-w-[3.5rem] max-w-[3.5rem] md:max-h-[7rem] md:min-h-[7rem] md:max-w-[6rem] md:min-w-[6rem] py-[0.4rem] px-[0.5rem] items-center rounded-[0.5rem] border-solid border-[0.67px] border-[#F7C9B0] bg-primary-light">
    <div class="resep-logo w-[2.8rem] h-[2.8rem] rounded-[0.4rem] md:p-[2rem]">
        <span class="material-icons-round !text-2 text-accent-dark md:!text-[2.5rem]">lunch_dining</span>
    </div>
    <p class="text-center self-stretch font-jakarta text-[0.45rem]/[120%] font-normal md:text-[0.75rem] overflow-ellipsis truncate">{{ $bahan->nama }}</p>
</div>